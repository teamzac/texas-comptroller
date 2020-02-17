<?php

namespace TeamZac\TexasComptroller\BeverageTax\Reports;

use TeamZac\TexasComptroller\Traits\AttributeBag;

class ReportPeriod
{
    use AttributeBag;
    
    protected $casts = [
        'month' => 'date',
        'gross_receipts' => 'double',
        'sales_tax' => 'double',
        'total_tax' => 'double',
    ];
}
