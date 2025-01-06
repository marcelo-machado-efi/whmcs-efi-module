<?php

namespace PaymentGateway\Factories;

use PaymentGateway\Methods\Boleto\BoletoPayment;
use PaymentGateway\Methods\PaymentMethodStrategy;
use PaymentGateway\DTOs\Client\ClientDTOFactory;
use PaymentGateway\DTOs\Config\ConfigAPIDTO;
use PaymentGateway\Methods\Boleto\BoletoConfig;
use PaymentGateway\Methods\Boleto\MetadataBillet;
use PaymentGateway\Methods\Boleto\Discount;
use PaymentGateway\Methods\Boleto\Configuration;
use PaymentGateway\Models\Invoice\WHMCSInvoice;
use PaymentGateway\Database\BoletoDAO;

class BoletoPaymentFactory
{
    public static function create(array $orderAttributes): PaymentMethodStrategy
    {
        $client = ClientDTOFactory::getClientDTO($orderAttributes, 'boleto');
        $configApi = new ConfigAPIDTO($orderAttributes);
        $metadata = new MetadataBillet($orderAttributes);
        $discount = new Discount($orderAttributes);
        $configuration = new Configuration($orderAttributes);
        $invoice = new WHMCSInvoice($orderAttributes['invoiceid']);
        $boletoConfig = new BoletoConfig($orderAttributes);
        $databaseInteraction = new BoletoDAO();

        return new BoletoPayment(
            $client,
            $configApi,
            $boletoConfig,
            $metadata,
            $discount,
            $configuration,
            $invoice,
            $databaseInteraction
        );
    }
}
