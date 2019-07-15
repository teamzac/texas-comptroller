<?php

namespace TeamZac\TexasComptroller\Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use TeamZac\TexasComptroller\SalesTax\Reports\ReportPeriod;
use TeamZac\TexasComptroller\SalesTax\Reports\AllocationDetail;
use TeamZac\TexasComptroller\SalesTax\Reports\HistoricalSummary;
use TeamZac\TexasComptroller\SalesTax\SummaryReports\ComparisonSummary;

class SalesTaxReportsTest extends TestCase
{
    /** @test */
    public function allocation_detail_report_test()
    {
        $this->markTestSkipped();
        $report = AllocationDetail::make()->forCity('Hudson Oaks')->get();
        
        // this report should always have 24 months
        $this->assertCount(24, $report);
        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->total_collections));
            $this->assertTrue(is_numeric($period->prior_period_collections));
            $this->assertTrue(is_numeric($period->current_period_collections));
            $this->assertTrue(is_numeric($period->future_period_collections));
            $this->assertTrue(is_numeric($period->audit_collections));
            $this->assertTrue(is_numeric($period->unidentified));
            $this->assertTrue(is_numeric($period->service_fee));
            $this->assertTrue(is_numeric($period->current_retained));
            $this->assertTrue(is_numeric($period->prior_retained));
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    public function allocation_detail_report_conties_test()
    {
        $this->markTestSkipped();
        $report = AllocationDetail::make()->forCounty('Parker County')->get();
        
        // this report should always have 24 months
        $this->assertCount(24, $report);
        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->total_collections));
            $this->assertTrue(is_numeric($period->prior_period_collections));
            $this->assertTrue(is_numeric($period->current_period_collections));
            $this->assertTrue(is_numeric($period->future_period_collections));
            $this->assertTrue(is_numeric($period->audit_collections));
            $this->assertTrue(is_numeric($period->unidentified));
            $this->assertTrue(is_numeric($period->service_fee));
            $this->assertTrue(is_numeric($period->current_retained));
            $this->assertTrue(is_numeric($period->prior_retained));
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }
    
    /** @test */
    public function allocation_detail_report_transit_authorities_test()
    {
        $this->markTestSkipped();
        $report = AllocationDetail::make()->forTransitAuthority('Parker County')->get();
        
        // this report should always have 24 months
        $this->assertCount(24, $report);
        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->total_collections));
            $this->assertTrue(is_numeric($period->prior_period_collections));
            $this->assertTrue(is_numeric($period->current_period_collections));
            $this->assertTrue(is_numeric($period->future_period_collections));
            $this->assertTrue(is_numeric($period->audit_collections));
            $this->assertTrue(is_numeric($period->unidentified));
            $this->assertTrue(is_numeric($period->service_fee));
            $this->assertTrue(is_numeric($period->current_retained));
            $this->assertTrue(is_numeric($period->prior_retained));
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    public function historical_summary_report_test()
    {
        $this->markTestSkipped();
        $report = HistoricalSummary::make()->forCity('Hudson Oaks')->get();
        
        // this report contains 23 full years plus the current year
        $this->assertEquals(23 * 12, $report->count() - ($report->count() % 12));

        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    function comparison_report_cities_test()
    {
        $report = ComparisonSummary::make()->forCities()->get();
        
        // this report contains 23 full years plus the current year
        $this->assertEquals(23 * 12, $report->count() - ($report->count() % 12));

        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    function comparison_report_counties_test()
    {
        $report = ComparisonSummary::make()->forCounties()->get();
        
        // this report contains 23 full years plus the current year
        $this->assertEquals(23 * 12, $report->count() - ($report->count() % 12));

        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    function comparison_report_transit_test()
    {
        $report = ComparisonSummary::make()->forTransitAuthorities()->get();
        
        // this report contains 23 full years plus the current year
        $this->assertEquals(23 * 12, $report->count() - ($report->count() % 12));

        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    function comparison_report_special_districts_test()
    {
        $report = ComparisonSummary::make()->forSpecialDistricts()->get();
        
        // this report contains 23 full years plus the current year
        $this->assertEquals(23 * 12, $report->count() - ($report->count() % 12));

        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }
}
