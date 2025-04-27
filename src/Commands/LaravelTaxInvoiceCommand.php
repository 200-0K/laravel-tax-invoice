<?php

namespace Err0r\LaravelTaxInvoice\Commands;

use Illuminate\Console\Command;

class LaravelTaxInvoiceCommand extends Command
{
    public $signature = 'laravel-tax-invoice';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
