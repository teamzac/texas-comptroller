<?php

namespace TeamZac\TexasComptroller\SalesTax\Reports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use TeamZac\TexasComptroller\BaseReports\HttpReport;
use TeamZac\TexasComptroller\Support\Currency;

class HistoricalSummary extends HttpReport
{
    protected $baseUri = 'https://mycpa.cpa.state.tx.us/allocation/';

    /**
     * Fetch results for a city
     * 
     * @param   string $name    the query string to search for
     * @return  $this
     */
    public function forCity($name)
    {
        $this->endpoint = 'CtyCntyAllocResults';

        $this->params = [
            'cityCountyName' => $name,
            'cityCountyOption' => 'City'
        ];

        return $this;
    }

    /**
     * Fetch results for a county
     * 
     * @param   string $name    the query string to search for
     * @return  $this
     */
    public function forCounty($name)
    {
        $this->endpoint = 'CtyCntyAllocResults';

        $this->params = [
            'cityCountyName' => $name,
            'cityCountyOption' => 'County'
        ];

        return $this;
    }

    /**
     * Fetch results for a transit district
     * 
     * @param   string $name    the query string to search for
     * @return  $this
     */
    public function forTransitAuthority($name)
    {
        $this->endpoint = 'MCCAllocResults';

        $this->params = [
            'mccOption' => 'MCC',
            'mccOptions' => $name
        ];

        return $this;
    }

    /**
     * Fetch results for a special district
     * 
     * @param   string $name
     * @return  $this
     */
    public function forSpecialDistrict($name)
    {
        $this->endpoint = 'SPDAllocResults';

        $this->params = [
            'spdOption' => 'SPD',
            'spdOptions' => $name
        ];

        return $this;
    }


    /**
     * Process the raw Psr7 response response
     * 
     * @param   GuzzleHttp\Psr7\Stream
     * @return  mixed
     */
    public function processRawResponse($response)
    {
        return (string) $response;
    }

    /**
     * Parse the response and return a collection of periods    
     *
     * @param   string $response
     * @return  Illuminate\Support\Collection
     */
    protected function parseResponse($response)
    {
        $now = new Carbon;
        $crawler = new Crawler($response);

        $periods = $crawler->filter('.resultsTable')->each(function(Crawler $table, $i) use ($now) {
            $year = $table->filter('thead th span')->getNode(0)->textContent;

            $rows = $table->filter('tbody tr')->each(function(Crawler $row, $i) use ($year, $now) {
                $month = trim($row->children()->getNode(0)->textContent);
                $amount = trim($row->children()->getNode(1)->textContent);

                if (strlen($month) === 0 || Str::contains($month, 'TOTAL') || Str::contains($month, '&nbsp') || Str::is($month, " ")) {
                    return;
                }
                $month = $this->mapTextToDate("{$month} {$year}");

                if ($month->gt($now) || $amount === '.') {
                    return;
                }

                return new ReportPeriod([
                    'month' => $month->format('Y-m-d'),
                    'net_payment' => Currency::clean($amount)
                ]);
            });
            return collect($rows)->filter(function($row) {
                return $row instanceof ReportPeriod;
            });
        });

        return collect($periods)->flatten()->sortByDesc(function($period) {
            return $period->month;
        })->values();
    }

    /**
     * Map the given text to a Carbon date object
     * 
     * @param   string $text
     * @return  Carbon\Carbon
     */
    protected function mapTextToDate($text)
    {
        return new Carbon($text);
    }
}
