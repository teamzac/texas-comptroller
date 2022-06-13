<?php

namespace TeamZac\TexasComptroller\Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use TeamZac\TexasComptroller\SalesTax\SummaryReports\SummaryPeriod;
use TeamZac\TexasComptroller\Support\MultipleEntityReportCollection;
use TeamZac\TexasComptroller\SalesTax\SummaryReports\ComparisonSummary;

class ComparisonSummaryTest extends TestCase
{
    /** @test */
    function comparison_report_cities_test()
    {
        $report = ComparisonSummary::make()
            ->forCities()
            ->get();

        tap($report->first(), function($period) {
            $this->assertInstanceOf(SummaryPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    function comparison_report_counties_test()
    {
        $report = ComparisonSummary::make()
            ->forCounties()
            ->get();

        tap($report->first(), function($period) {
            $this->assertInstanceOf(SummaryPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    function comparison_report_transit_test()
    {
        $report = ComparisonSummary::make()
            ->forTransitAuthorities()
            ->get();

        tap($report->first(), function($period) {
            $this->assertInstanceOf(SummaryPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }

    /** @test */
    function comparison_report_special_districts_test()
    {
        $report = ComparisonSummary::make()
            ->forSpecialDistricts()
            ->get();

        tap($report->first(), function($period) {
            $this->assertInstanceOf(SummaryPeriod::class, $period);
            $this->assertInstanceof(Carbon::class, $period->month);
            $this->assertTrue(is_numeric($period->net_payment));
        });
    }
}
