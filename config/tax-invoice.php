<?php

// config for Err0r/LaravelTaxInvoice
return [
    'currency' => env('TAX_INVOICE_CURRENCY', 'SAR'),
    'logo' => env('TAX_INVOICE_LOGO', null),
    
    /*
    * TODO" REMOVE IF NOT NEEDED
    * Default attributes for Seller::class
    */
    // 'seller' => [
    //     'name' => env('TAX_INVOICE_SELLER_NAME'),
    //     'address' => env('TAX_INVOICE_SELLER_ADDRESS'),
    //     'code' => env('TAX_INVOICE_SELLER_CODE'),
    //     'vat' => env('TAX_INVOICE_SELLER_VAT'),
    //     'phone' => env('TAX_INVOICE_SELLER_PHONE'),
    // ],
];
