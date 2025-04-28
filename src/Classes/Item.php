<?php

namespace Err0r\LaravelTaxInvoice\Classes;

class Item
{
    public function __construct(
        public string $name,
        public float $price,
        public int $quantity,
    ) {}
}