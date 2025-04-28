<?php

namespace Err0r\LaravelTaxInvoice;

use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use Carbon\Carbon;
use Err0r\LaravelTaxInvoice\Classes\Customer;
use Err0r\LaravelTaxInvoice\Classes\Invoice;
use Err0r\LaravelTaxInvoice\Classes\Item;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;

class LaravelTaxInvoice
{
    /**
     * @param  array<Item>  $items
     */
    public function invoice(
        float $tax,
        Customer $user,
        array $items,
        Carbon $date,
        string $id,
        string $status,
        string $paymentMethod,
        float $discount = 0,
    ) {
        $customer = new Buyer([
            'name' => $user->name ?? 'N/A',
            'custom_fields' => [
                'email' => $user->email ?? 'N/A',
                'phone' => $user->phone ?? 'N/A',
            ],
        ]);

        $invoiceItems = collect($items)->map(function (Item $item) use ($tax) {
            return (new InvoiceItem)
                ->title($item->name)
                ->pricePerUnit($item->price / ($tax + 1))
                ->quantity($item->quantity);
        });

        $orderTotalWithTax = round(collect($items)->sum(fn (Item $item) => $item->price * $item->quantity),2);
        $orderTotalWithoutTax = round($orderTotalWithTax / ($tax + 1), 2);
        $orderTotalTax = round($orderTotalWithTax - $orderTotalWithoutTax, 2);

        $qrCode = GenerateQrCode::fromArray([
            new Seller(config('entity.seller.name')), // seller name
            new TaxNumber(config('entity.seller.vat')), // seller VAT number
            new InvoiceDate($date->format('c')), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount(round($orderTotalWithTax, 2)), // invoice total amount with vat
            new InvoiceTaxAmount($orderTotalTax), // invoice tax amount
        ])->render();

        $invoice = Invoice::make()
            ->filename("ORD-$id")
            ->status($status)
            ->id($id)
            // ->sequence($order->id)
            ->date($date)
            ->dateFormat('M j, Y')
            ->currencySymbol('ر.س.')
            ->buyer($customer)
            ->taxRate($tax * 100)
            // ->shipping(1.99)
            ->addItems($invoiceItems)
            ->payUntilDays(0)
            // ->totalTaxes($order->total_price)
            ->logo(public_path('assets/logo.png'))
            ->paymentMethod($paymentMethod)
            ->qrCode($qrCode);

        if ($discount > 0) {
            $invoice->totalDiscount(
                round(
                    $discount,
                    2
                )
            );
        }

        return $invoice;
    }
}
