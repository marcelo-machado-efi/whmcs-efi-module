<?php

namespace PaymentGateway\Methods;

use PaymentGateway\Methods\PaymentMethodStrategy;

class OpenFinancePayment implements PaymentMethodStrategy
{
    public function processPayment(array $invoiceData)
    {
        // Implementação do pagamento via Open Finance
        return "Open Finance payment processed for invoice: " . $invoiceData['id'];
    }

    public function processRefund(string $transactionId)
    {
        // Implementação do estorno via Open Finance
        return "Open Finance refund processed for transaction: " . $transactionId;
    }

    public function setupSubscription(array $subscriptionData)
    {
        // Implementação da assinatura via Open Finance
        return "Open Finance subscription setup for: " . $subscriptionData['id'];
    }
}
