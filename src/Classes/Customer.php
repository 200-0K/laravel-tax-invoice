<?php

namespace Err0r\LaravelTaxInvoice\Classes;

class Customer
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
    ) {}
}