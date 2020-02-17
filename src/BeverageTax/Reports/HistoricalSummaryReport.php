<?php

namespace TeamZac\TexasComptroller\BeverageTax\Reports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use TeamZac\TexasComptroller\BaseReports\HttpReport;
use TeamZac\TexasComptroller\Support\Currency;

class HistoricalSummaryReport extends HttpReport
{
    protected $baseUri = 'https://mycpa.cpa.state.tx.us/allocation/CtyCntyAllocMixBevResults';

    /**
     * Fetch results for a city
     * 
     * @param   string $name    the query string to search for
     * @return  $this
     */
    public function forCity($name)
    {
        $this->endpoint = 'CtyCntyAllocMixBevResults';

        $this->params = [
            'cityCountyName' => $name,
            'cityCountyOption' => 'City',
            'summaryType' => 'Gross Receipts',
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
        $this->endpoint = 'CtyCntyAllocMixBevResults';

        $this->params = [
            'cityCountyName' => $name,
            'cityCountyOption' => 'County'
        ];

        return $this;
    }

    public function get()
    {
        $summaryTypes = ['Sales Tax', 'Gross Receipts', 'Total Taxes'];
        $collections = collect();

        foreach($summaryTypes as $type) {
            $this->params['summaryType'] = $type;
            $collections[] = parent::get();
        }

        return $this->merge($collections);
    }

    protected function merge($collections)
    {
        $merged = collect();

        $count = $collections->first()->count();

        for ($i = 0; $i < $count; $i++) {
            $mergedPeriod = new ReportPeriod;
            foreach ($collections as $summaryType) {
                $mergedPeriod->setAttributes($summaryType[$i]->getAttributes());
            }
            $merged[] = $mergedPeriod;
        }
        return $merged;
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
                    Str::snake($this->params['summaryType']) => Currency::clean($amount),
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
