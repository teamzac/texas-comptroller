<?php

namespace TeamZac\TexasComptroller\Tests;

use PHPUnit\Framework\TestCase;
use TeamZac\TexasComptroller\SalesTax\Search\TaxpayerSearch;


class TaxpayerSearchTest extends TestCase
{
    /** @test */
    public function search_test()
    {
        $taxpayer = TaxpayerSearch::make()->search(17430193866);

        $this->assertSame('17430193866', $taxpayer->id);
        $this->assertSame('WAL-MART STORES TEXAS, LLC', $taxpayer->name);
        $this->assertSame('702 SW 8TH ST C/O SALES TX #0500', $taxpayer->address['street']);
        $this->assertSame('WAL-MART STORE #77', $taxpayer->locations->first()['name']);
    }
}
