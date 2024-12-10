<?php

namespace PaymentGateway\Factories;

use PaymentGateway\Methods\PaymentMethodStrategy;
use PaymentGateway\DTOs\Client\ClientDTOFactory;
use PaymentGateway\DTOs\Config\ConfigAPIDTO;
use PaymentGateway\Methods\Boleto\BoletoConfig;
use PaymentGateway\Methods\Boleto\Metadata;
use PaymentGateway\Models\Invoice\WHMCSInvoice;
use PaymentGateway\Database\BoletoDAO;
use PaymentGateway\Methods\CreditCardPayment;

class CreditCardPaymentFactory
{
    public static function create(array $orderAttributes): PaymentMethodStrategy
    {
        $client = ClientDTOFactory::getClientDTO($orderAttributes, 'cartao');
        $configApi = new ConfigAPIDTO($orderAttributes);
        $metadata = new Metadata($orderAttributes);
        $invoice = new WHMCSInvoice($orderAttributes['invoiceid']);
        $boletoConfig = new BoletoConfig($orderAttributes);
        $databaseInteraction = new BoletoDAO();

        return new CreditCardPayment(
            $client,
            $configApi,
            $boletoConfig,
            $metadata,
            $invoice,
            $databaseInteraction
        );
    }
}
