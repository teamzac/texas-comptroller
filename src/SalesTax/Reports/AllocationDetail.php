<?php

namespace TeamZac\TexasComptroller\SalesTax\Reports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use TeamZac\TexasComptroller\BaseReports\HttpReport;
use TeamZac\TexasComptroller\Support\Currency;

class AllocationDetail extends HttpReport
{
    protected $authorityCode;

    /**
     * Fetch report for a city
     * 
     * @param   string $name    the query string to search for
     * @return  $this
     */
    public function forCity($name)
    {
        $this->endpoint = 'CtyCntyAllDtlResults';

        $this->params = [
            'cityCountyName' => $name,
            'cityCountyOption' => 'City'
        ];

        return $this;
    }

    /**
     * Fetch report for a county
     * 
     * @param   string $name    the query string to search for
     * @return  $this
     */
    public function forCounty($name)
    {
        $this->endpoint = 'CtyCntyAllDtlResults';

        $this->params = [
            'cityCountyName' => $name,
            'cityCountyOption' => 'County'
        ];

        return $this;
    }

    /**
     * Fetch report for a transit district
     * 
     * @param   string $name    the query string to search for
     * @return  $this
     */
    public function forTransitAuthority($name)
    {
        $this->endpoint = 'MCCAllocDtlResults';

        $this->params = [
            'mccOption' => 'MCC',
            'mccOptions' => $name
        ];

        return $this;
    }

    /**
     * Fetch report for a special district
     * 
     * @param   string $name
     * @return  $this
     */
    public function forSpecialDistrict($name)
    {
        $this->endpoint = 'SPDAllocDtlResults';

        $this->params = [
            'spdOption' => 'SPD',
            'spdOptions' => $name
        ];

        return $this;
    }

    /**
     * Parse the response and return a collection of periods    
     *
     * @param   string $response
     * @return  Collection
     */
    protected function parseResponse($response)
    {
        $crawler = new Crawler($response);

        $this->setAuthorityCode($crawler);
        
        $periods = Collection::make([]);
        $periods = $crawler->filter('.resultsTable')->each(function(Crawler $node, $i) {
            $attributes = [
                'month' => $this->mapTextToDate($node->filter('th')->getNode(0)->textContent),
            ];

            $components = $node->filter('tbody tr')->each(function($row, $i) use (&$attributes) {
                $componentColumn = $row->children()->getNode(0);
                $amountColumn = $row->children()->getNode(1);

                $attributes[$this->cleanComponent($componentColumn->textContent)] = Currency::clean($amountColumn->textContent);
            });
            return new ReportPeriod($attributes);
        });

        return collect($periods)->sortByDesc(function($period) {
            return $period->month;
        });
    }

    /**
     * Map the given text to a Carbon date object
     * 
     * @param   string $text
     * @return  Carbon
     */
    protected function mapTextToDate($text)
    {
        $matches = [];
        if (false === preg_match('([A-Za-z]{3} [0-9]{4})', $text, $matches)) {
            throw new \Exception('Could not match a date in text: ' . $text);
        }

        return new Carbon($matches[0]);
    }

    /**
     * Get the authority code, which is saved while parsing the response
     * 
     * @return  integer
     */
    public function getAuthorityCode()
    {
        return $this->authorityCode;
    }

    /**
     * Fetch and set the authority code
     * 
     * @param   $crawler
     * @return  void
     */
    public function setAuthorityCode($crawler)
    {
        try {
            $text = $crawler->filter('.resultspageCriteria')->getNode(0)->textContent;
        } catch (\Exception $e) {
            return;
        }

        $matches = [];
        preg_match('/Authority Code: ([0-9]*)/', $text, $matches);

        if (count($matches) == 2) {
            $this->authorityCode = trim($matches[1]);
        }
    }

    /**
     * Remote stupid abbreviations and other punctionation
     * 
     * @param   string $text
     * @return  string
     */
    public function cleanComponent($text)
    {
        return Str::snake(
            str_replace('prd', 'period', 
                str_replace(':', '', strtolower($text))
            )
        );
    }
}
