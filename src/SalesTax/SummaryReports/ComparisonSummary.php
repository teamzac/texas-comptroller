<?php

namespace TeamZac\TexasComptroller\SalesTax\SummaryReports;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use TeamZac\TexasComptroller\Support\JsonReport;

class ComparisonSummary extends JsonReport
{
    protected $baseUri = 'https://www.comptroller.texas.gov/transparency/local/allocations/sales-tax/';

    protected $params = [];

    /**
     * The report type
     *
     * @var string (city|county|transit|spd)
     */
    protected $reportType;

    /**
     * Get the report for cities
     * 
     * @return  this
     */
    public function forCities()
    {
        $this->endpoint = 'scripts/cities-load.php';

        $this->reportType = 'city';

        return $this;
    }

    /**
     * Get the report for counties
     * 
     * @return  this
     */
    public function forCounties()
    {
        $this->endpoint = 'scripts/counties-load.php';

        $this->reportType = 'county';

        return $this;
    }

    /**
     * Get the report for transit authorities
     * 
     * @return  this
     */
    public function forTransitAuthorities()
    {
        $this->endpoint = 'transit/scripts/transit-load.php';

        $this->reportType = 'transit';

        return $this;
    }

    /**
     * Get the report for special districts
     * 
     * @return  
     */
    public function forSpecialDistricts()
    {
        $this->endpoint = 'special-district/scripts/spd-load.php';

        $this->reportType = 'spd';

        return $this;
    }

    /**
     * Parse the response and return a collection of periods    
     *
     * @param   string $response
     * @return  array
     */
    protected function parseResponse($json)
    {
        $reportMonth = $this->getReportMonth($json);
        
        $entities = collect($json)->map(function($row) use ($reportMonth) {
            return $this->parseRow($row, $reportMonth);
        })->sortBy('entity');

        return [
            'month' => $reportMonth,
            'entities' => $entities
        ];
    }

    /**
     * Get the month that this report represents
     * 
     * @param   
     * @return  Carbon\Carbon
     */
    public function getReportMonth($json)
    {
        $firstEntry = $json[0];

        return new Carbon( sprintf("%s/1/%s", $firstEntry->report_month, $firstEntry->report_year) );
    }

    /**
     * Map the given text to a Carbon date object
     * 
     * @param   string $text
     * @return  
     */
    protected function mapTextToDate($text, $format='Y-m-d')
    {
        return (new Carbon($text))->format($format);
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function parseRow($row, $reportMonth)
    {
        $keys = $this->getHashKeys();

        return [
            'entity' => $row->{$keys['entity']},
            'month' => $reportMonth,
            'net_payment' => $row->{$keys['net_payment']},
            'net_payment_delta' => isset($row->{$keys['net_payment_delta']}) ? $row->{$keys['net_payment_delta']} : 0,
            'prior_period' => $row->{$keys['prior_period']},
            'year_to_date' => $row->{$keys['year_to_date']},
            'year_to_date_delta' => isset($row->{$keys['year_to_date_delta']}) ? $row->{$keys['year_to_date_delta']} : 0
        ];
    }

    /**
     * Check to see if this is the city report
     * 
     * @return  Bool
     */
    public function isCityReport()
    {
        return $this->reportType == 'city';
    }

    /**
     * Get the key hash for this specific report type, since the keys
     * are different for some reason that makes absolutely no sense
     * 
     * @return  array
     */
    public function getHashKeys()
    {
        if ( $this->reportType == 'city' ) 
        {
            return [
                'entity' => 'city',
                'net_payment' => 'net_payment_this_period',
                'net_payment_delta' => 'period_percent_change',
                'prior_period' => 'comparable_payment_prior_year',
                'year_to_date' => 'payments_to_date',
                'year_to_date_delta' => 'ytd_percent_change'
            ];
        }
        else 
        {
            return [
                'entity' => 'name',
                'net_payment' => 'net_payment_this_period',
                'net_payment_delta' => 'percent_change_prior_year',
                'prior_period' => 'comparable_payment_prior_year',
                'year_to_date' => 'payments_to_date',
                'year_to_date_delta' => 'percent_change_to_date'
            ];
        }
    }
}