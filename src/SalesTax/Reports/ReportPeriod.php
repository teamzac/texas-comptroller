<?php

namespace TeamZac\TexasComptroller\SalesTax\Reports;

use TeamZac\TexasComptroller\Traits\AttributeBag;

class ReportPeriod
{
    use AttributeBag;
    
    protected $casts = [
        'month' => 'date',
        'total_collections' => 'double',
        'prior_period_collections' => 'double',
        'current_period_collections' => 'double',
        'future_period_collections' => 'double',
        'audit_collections' => 'double',
        'single_local_rate_collections' => 'double',
        'unidentified' => 'double',
        'service_fee' => 'double',
        'current_retained' => 'double',
        'prior_retained' => 'double',
        'net_payment' => 'double',
    ];
}
