<?php

return [
    'name' => 'BankTransferPayment',

    /*
    |--------------------------------------------------------------------------
    | Default Payment Settings
    |--------------------------------------------------------------------------
    */
    'settings' => [
        'enabled' => true,
        'title' => 'Bank Transfer',
        'description' => 'Pay via bank transfer. You will receive bank account details after placing your order.',
        'instructions' => 'Please make the payment to the following bank account and include your order number as reference.',
        'bank_name' => '',
        'account_holder' => '',
        'iban' => '',
        'bic' => '',
        'additional_info' => '',
        'order_status' => 'pending',
        'sort_order' => 0,
    ],
];