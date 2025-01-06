<?php

namespace PaymentGateway\Factories;

use PaymentGateway\Methods\PaymentMethodStrategy;
use PaymentGateway\DTOs\Client\ClientDTOFactory;
use PaymentGateway\DTOs\Config\ConfigAPIDTO;
use PaymentGateway\Methods\CreditCard\MetadataCreditCard;
use PaymentGateway\Models\Invoice\WHMCSInvoice;
use PaymentGateway\Database\CreditCardDAO;
use PaymentGateway\Methods\CreditCard\CreditCardConfig;
use PaymentGateway\Methods\CreditCard\CreditCardPayment;
use PaymentGateway\Methods\CreditCard\PaymentToken;

class CreditCardPaymentFactory
{
    public static function create(array $orderAttributes): PaymentMethodStrategy
    {
        $client = ClientDTOFactory::getClientDTO($orderAttributes, 'cartao');
        $configApi = new ConfigAPIDTO($orderAttributes);
        $metadata = new MetadataCreditCard($orderAttributes);
        $paymentToken = new PaymentToken($orderAttributes['paramsCartao']['payment_token']);
        $invoice = new WHMCSInvoice($orderAttributes['invoiceid']);
        $boletoConfig = new CreditCardConfig($orderAttributes);
        $databaseInteraction = new CreditCardDAO();

        return new CreditCardPayment(
            $client,
            $configApi,
            $boletoConfig,
            $invoice,
            $databaseInteraction,
            $metadata,
            $paymentToken,
        );
    }
}
