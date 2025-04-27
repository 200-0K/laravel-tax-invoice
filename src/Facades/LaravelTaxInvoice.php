<?php

namespace Err0r\LaravelTaxInvoice\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Err0r\LaravelTaxInvoice\LaravelTaxInvoice
 */
class LaravelTaxInvoice extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Err0r\LaravelTaxInvoice\LaravelTaxInvoice::class;
    }
}
