<?php

namespace TeamZac\TexasComptroller\Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use TeamZac\TexasComptroller\SalesTax\Reports\ReportPeriod;
use TeamZac\TexasComptroller\SalesTax\Reports\HistoricalSummary;

class HistoricalSummaryTest extends TestCase
{
    /** @test */
    public function historical_summary_report_city_test()
    {
        $report = HistoricalSummary::make()
            ->forCity('Hudson Oaks')
            ->get();
        
        // this report contains 23 full years plus the current year
        $this->assertEquals(23 * 12, $report->count() - ($report->count() % 12));

        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    public function historical_summary_report_county_test()
    {
        $report = HistoricalSummary::make()
            ->forCounty('Parker')
            ->get();
        
        // this report contains 23 full years plus the current year
        $this->assertEquals(23 * 12, $report->count() - ($report->count() % 12));

        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    public function historical_summary_report_transit_authority_test()
    {
        $report = HistoricalSummary::make()
            ->forTransitAuthority('Austin MTA')
            ->get();
        
        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    public function historical_summary_report_special_district_test()
    {
        // $report = HistoricalSummary::make()->get();
        
        // tap($report->first(), function($period) {
        //     $this->assertInstanceOf(ReportPeriod::class, $period);
        //     $this->assertInstanceof(Carbon::class, $period->month);
        //     $this->assertTrue(is_numeric($period->net_payment));
        // });
    }
}
