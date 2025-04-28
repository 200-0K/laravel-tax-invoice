<?php

namespace Err0r\LaravelTaxInvoice\Classes;

use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use LaravelDaily\Invoices\Invoice as LaravelInvoice;
use Symfony\Component\HttpFoundation\File\File;

class Invoice extends LaravelInvoice
{
    public $qrCode;
    public $id;
    public $paymentMethod;

    public function __construct($name = '')
    {
        parent::__construct($name);
    }

    /**
     * @param string $name
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return Invoice
     */
    public static function make($name = '')
    {
        return new self($name);
    }

    public function render()
    {
        if ($this->pdf !== null) {
            return $this;
        }

        $this->beforeRender();

        $template = sprintf('tax-invoice::templates.%s', $this->template);

        // @phpstan-ignore assign.propertyType
        $this->pdf = PDF::loadView($template, ['invoice' => $this]);

        $this->output = $this->pdf->output();

        return $this;
    }

    public function output()
    {
        $this->render();

        return $this->output;
    }

    public function qrCode($qrCode)
    {
        $this->qrCode = $qrCode;

        return $this;
    }

    public function getQrCode()
    {
        return $this->qrCode;
    }

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function paymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
}
