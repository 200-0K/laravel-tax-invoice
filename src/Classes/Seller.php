<?php

namespace Err0r\LaravelTaxInvoice\Classes;

class Seller
{
    public function __construct(
        public ?string $name = null,
        public ?string $address = null,
        public ?string $code = null,
        public ?string $vat = null,
        public ?string $phone = null,
        public ?string $email = null,
    ) {}
}