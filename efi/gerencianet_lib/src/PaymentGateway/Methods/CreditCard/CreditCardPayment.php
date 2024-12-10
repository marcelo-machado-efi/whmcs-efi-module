<?php

namespace PaymentGateway\Methods;

use PaymentGateway\Database\CreditCardDAO;
use PaymentGateway\DTOs\Client\ClientDTO;
use PaymentGateway\DTOs\Config\ConfigAPIDTO;
use PaymentGateway\Methods\PaymentMethodStrategy;
use PaymentGateway\Models\Invoice\WHMCSInvoice;

class CreditCardPayment implements PaymentMethodStrategy
{
    private ClientDTO $client;
    private CreditCardConfig $cardConfig;
    private ConfigAPIDTO $configConnectionApi;
    private WHMCSInvoice $invoice;
    private CreditCardDAO $databaseInteraction;

    public function processPayment(): mixed
    {
        // Implementação do pagamento via cartão de crédito
        return true;
    }

    public function processRefund(string $transactionId): bool
    {
        // Implementação do estorno via cartão de crédito
        return "Credit card refund processed for transaction: " . $transactionId;
    }

    public function setupSubscription(array $subscriptionData): bool
    {
        // Implementação da assinatura via cartão de crédito
        return "Credit card subscription setup for: " . $subscriptionData['id'];
    }
}
