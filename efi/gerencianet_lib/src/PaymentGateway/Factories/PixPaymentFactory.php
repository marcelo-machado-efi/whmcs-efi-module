<?php

namespace PaymentGateway\Factories;

use PaymentGateway\Methods\PaymentMethodStrategy;
use PaymentGateway\DTOs\Config\ConfigAPIDTO;
use PaymentGateway\Methods\Pix\Discount;
use PaymentGateway\Models\Invoice\WHMCSInvoice;
use PaymentGateway\Database\PixDAO;
use PaymentGateway\Methods\Pix\PixConfig;
use PaymentGateway\Methods\Pix\PixPayment;

class PixPaymentFactory
{
    public static function create(array $orderAttributes): PaymentMethodStrategy
    {
        $configApi = new ConfigAPIDTO($orderAttributes);
        $discount = new Discount($orderAttributes);
        $invoice = new WHMCSInvoice($orderAttributes['invoiceid']);
        $pixConfig = new PixConfig($orderAttributes);
        $databaseInteraction = new PixDAO();

        return new PixPayment(
            $configApi,
            $pixConfig,
            $discount,
            $invoice,
            $databaseInteraction
        );
    }
}
