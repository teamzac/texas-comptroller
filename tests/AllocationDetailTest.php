<?php

namespace TeamZac\TexasComptroller\Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use TeamZac\TexasComptroller\SalesTax\Reports\ReportPeriod;
use TeamZac\TexasComptroller\SalesTax\Reports\AllocationDetail;

class AllocationDetailTest extends TestCase
{
    /** @test */
    public function allocation_detail_report_test()
    {
        $report = AllocationDetail::make()->forCity('Hudson Oaks')->get();
        
        // this report should always have 24 months
        $this->assertCount(24, $report);
        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->total_period_collections));
            $this->assertTrue(is_numeric($period->prior_period_collections));
            $this->assertTrue(is_numeric($period->current_period_collections));
            $this->assertTrue(is_numeric($period->future_period_collections));
            $this->assertTrue(is_numeric($period->audit_collections));
            $this->assertTrue(is_numeric($period->single_local_rate_collections));
            $this->assertTrue(is_numeric($period->unidentified));
            $this->assertTrue(is_numeric($period->service_fee));
            $this->assertTrue(is_numeric($period->current_retained));
            $this->assertTrue(is_numeric($period->prior_retained));
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    public function allocation_detail_report_counties_test()
    {
        $report = AllocationDetail::make()->forCounty('Parker')->get();
        
        // this report should always have 24 months
        $this->assertCount(24, $report);
        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->total_period_collections));
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
        $report = AllocationDetail::make()->forTransitAuthority('Austin MTA')->get();
        
        // this report should always have 24 months
        $this->assertCount(24, $report);
        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->total_period_collections));
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
    public function allocation_detail_report_special_district_test()
    {
        $report = AllocationDetail::make()->forSpecialDistrict('Airline Improvement Dist')->get();
        
        // this report should always have 24 months
        $this->assertCount(24, $report);
        tap($report->first(), function($period) {
            $this->assertInstanceOf(ReportPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->total_period_collections));
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
}
