<?php

namespace Err0r\LaravelTaxInvoice;

use Carbon\Carbon;
use Err0r\LaravelTaxInvoice\Classes\Customer;
use Err0r\LaravelTaxInvoice\Classes\Invoice;
use Err0r\LaravelTaxInvoice\Classes\Item;
use Err0r\LaravelTaxInvoice\Classes\Seller;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Seller as InvoiceSeller;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller as ZATCASeller;
use Salla\ZATCA\Tags\TaxNumber;
use Illuminate\Http\Response;


class LaravelTaxInvoice
{
    /**
     * @param  array<Item>  $items
     */
    public function invoice(
        float $tax,
        Customer $customer,
        Seller $seller,
        array $items,
        Carbon $date,
        string $id,
        string $status,
        string $paymentMethod,
        float $discount = 0,
    ): Response {
        $customer = $this->getCustomer($customer);
        $invoiceSeller = $this->getSeller($seller);
        $invoiceItems = $this->getInvoiceItems($items, $tax);

        $orderTotalWithTax = round(collect($items)->sum(fn (Item $item) => $item->price * $item->quantity), 2);
        $orderTotalWithoutTax = round($orderTotalWithTax / ($tax + 1), 2);
        $orderTotalTax = round($orderTotalWithTax - $orderTotalWithoutTax, 2);

        $qrCode = GenerateQrCode::fromArray([
            new ZATCASeller($seller->name), // seller name
            new TaxNumber($seller->vat), // seller VAT number
            new InvoiceDate($date->format('c')), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount(round($orderTotalWithTax, 2)), // invoice total amount with vat
            new InvoiceTaxAmount($orderTotalTax), // invoice tax amount
        ])->render();

        $invoice = Invoice::make()
            ->filename("ORD-$id") // TODO: $filename
            ->status($status)
            ->id($id)
            // ->sequence($order->id)
            ->date($date)
            ->dateFormat('M j, Y') // TODO: $dateFormat
            ->currencySymbol('SAR')
            ->buyer($customer)
            ->seller($invoiceSeller)
            // ->taxRate($tax * 100) // TODO: $taxRate
            // ->taxableAmount(100)
            // ->shipping(1.99)
            ->addItems($invoiceItems)
            ->payUntilDays(0)
            // ->totalTaxes($order->total_price)
            ->logo(__DIR__ . '/../resources/images/logo.png') // TODO: $logo
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

        return $invoice->stream();
    }

    private function getSeller(Seller $seller)
    {
        $invoiceSeller = new InvoiceSeller();
        $invoiceSeller->name = $seller->name;
        $invoiceSeller->address = $seller->address;
        $invoiceSeller->code = $seller->code;
        $invoiceSeller->vat = $seller->vat;
        $invoiceSeller->phone = $seller->phone;

        return $invoiceSeller;
    }

    private function getCustomer(Customer $customer)
    {
        $customer = new Buyer([
            'name' => $customer->name ?? 'N/A',
            'custom_fields' => [
                'email' => $customer->email ?? 'N/A',
                'phone' => $customer->phone ?? 'N/A',
            ],
        ]);

        return $customer;
    }

    /**
     * @param  array<Item>  $items
     */
    private function getInvoiceItems(array $items, float $tax)
    {
        return collect($items)->map(function (Item $item) use ($tax) {
            $pricePerUnit = $item->price / ($tax + 1);

            return (new InvoiceItem)
                ->title($item->name)
                ->pricePerUnit($pricePerUnit)
                ->tax($item->price - $pricePerUnit)
                ->quantity($item->quantity);
        })->toArray();
    }
}
