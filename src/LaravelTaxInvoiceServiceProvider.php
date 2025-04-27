<?php

namespace Err0r\LaravelTaxInvoice;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Err0r\LaravelTaxInvoice\Commands\LaravelTaxInvoiceCommand;

class LaravelTaxInvoiceServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-tax-invoice')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_tax_invoice_table')
            ->hasCommand(LaravelTaxInvoiceCommand::class);
    }
}
