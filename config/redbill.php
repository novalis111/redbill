<?php

return [

    'invoice_prefix'       => env('INVOICE_NR_PREFIX', "Y-m-"),
    'invoice_increment'    => env('INVOICE_INCREMENT', 0),
    'invoice_title'        => env('INVOICE_TITLE', 'redbill.invoice_title'),
    'currency'             => env('CURRENCY', 'euro'),
    'currency_format'      => env('CURRENCY_FORMAT', "%s &euro;"),
    'default_tax_rate'     => env('DEFAULT_TAX_RATE', 19.0),
    'income_tax_rate'      => env('INCOME_TAX_RATE', 30.0),
    'invoice_overdue_days' => env('INVOICE_OVERDUE_DAYS', 10),
    'default_hour_price'   => 50,
    'bank_owner'           => env('BANK_OWNER', 'company'),

];
