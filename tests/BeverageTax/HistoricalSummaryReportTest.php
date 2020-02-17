<?php

namespace TeamZac\TexasComptroller\Tests\BeverageTax;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use TeamZac\TexasComptroller\BeverageTax\Reports\ReportPeriod;
use TeamZac\TexasComptroller\BeverageTax\Reports\HistoricalSummaryReport;

class HistoricalSummaryTest extends TestCase
{
    /** @test */
    public function historical_summary_report_city_test()
    {
        $report = HistoricalSummaryReport::make()->forCity('Hudson Oaks')->get();

        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->gross_receipts));
            $this->assertTrue(is_numeric($period->sales_tax));
            $this->assertTrue(is_numeric($period->total_taxes));
        });
    }

    /** @test */
    public function historical_summary_report_county_test()
    {
        $report = HistoricalSummaryReport::make()->forCounty('Parker')->get();

        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->gross_receipts));
            $this->assertTrue(is_numeric($period->sales_tax));
            $this->assertTrue(is_numeric($period->total_taxes));
        });
    }
}
