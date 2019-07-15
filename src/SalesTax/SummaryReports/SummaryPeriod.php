<?php

namespace TeamZac\TexasComptroller\SalesTax\SummaryReports;

use TeamZac\TexasComptroller\Traits\AttributeBag;

class SummaryPeriod
{
    use AttributeBag;
    
    protected $casts = [
        'month' => 'date',
        'net_payment' => 'double',
        'prior_year' => 'double',
        'net_payment_delta' => 'double',
        'year_to_date' => 'double',
        'year_to_date_delta' => 'double',
    ];
}
