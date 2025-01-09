<?php

namespace PaymentGateway\Methods;

interface PaymentMethodStrategy
{
    /**
     * Processa o pagamento de uma fatura.
     *
     * @return mixed Resultado do pagamento
     */
    public function processPayment(): mixed;

    /**
     * Realiza um estorno para o pagamento.
     *
     * @param string $transactionId ID da transação para estorno
     * @return mixed Resultado do estorno
     */
    public function processRefund(string $transactionId): mixed;

    /**
     * Configura uma assinatura para o método de pagamento.
     *
     * @param array $subscriptionData Dados para configuração da assinatura
     * @return mixed Resultado da assinatura
     */
    public function setupSubscription(array $subscriptionData): mixed;
}
