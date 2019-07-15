<?php

namespace TeamZac\TexasComptroller\SalesTax\Reports;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use TeamZac\TexasComptroller\Support\Currency;
use TeamZac\TexasComptroller\BaseReports\HttpReport;

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
        $periods = Collection::make([]);

        $domParser = str_get_html($response);
        $tables = $domParser->find('.resultsTable');
        foreach ($tables as $table) {
            $year = $table->find('thead th span')[0]->innertext;

            $rows = $table->find('tbody tr');
            array_shift($rows);

            foreach ($rows as $row) {
                $columns = $row->find('td');

                if ( count($columns) < 2 ) {
                    continue;
                }

                list($monthColumn, $amountColumn) = $columns;
                $month = trim($monthColumn->innertext);

                if (strlen($month) == 0 || Str::contains($month, 'TOTAL') || Str::contains($month, '&nbsp')) {
                    continue;
                }

                $month = $this->mapTextToDate("{$month} {$year}");

                if ($month->gt($now)) {
                    continue;
                }

                $periods[] = new ReportPeriod([
                    'month' => $month->format('Y-m-d'),
                    'net_payment' => Currency::clean($amountColumn->innertext)
                ]);
            }
        }

        return $periods->sortByDesc(function($period) {
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
