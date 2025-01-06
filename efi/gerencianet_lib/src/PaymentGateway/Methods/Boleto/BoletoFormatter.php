<?php

namespace PaymentGateway\Methods\Boleto;

use PaymentGateway\Logging\TransactionLogger;
use PaymentGateway\Methods\Interfaces\Metadata;

class BoletoFormatter
{
    /**
     * Formata o objeto BankingBillet para um array.
     *
     * @param BankingBillet $payment Objeto de pagamento a ser formatado.
     * @return array Estrutura formatada do boleto.
     */
    public function format(BankingBillet $payment): array
    {
        try {
            $items = $this->formatItems($payment->getItems());
            $metadata = $this->formatMetadata($payment->getMetadata());
            $customer = $this->formatCustomer($payment);
            $discount = $this->formatDiscount($payment->getDiscount());
            $configurations = $this->formatConfiguration($payment->getConfiguration());

            $bankingBillet = [
                "expire_at" => $payment->getExpireAt(),
                "customer" => $customer,
                "configurations" => $configurations,
            ];

            if (!empty($payment->getMessage())) {
                $bankingBillet["message"] = $payment->getMessage();
            }

            if ($discount['value'] > 0) {
                $bankingBillet["discount"] = $discount;
            }

            return [
                'items' => $items,
                'metadata' => $metadata,
                'payment' => [
                    "banking_billet" => $bankingBillet
                ],
            ];
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return [];
        }
    }

    private function formatItems(array $items): array
    {
        return array_map(function (Item $item) {
            return [
                'name' => $item->getName(),
                'value' => $item->getValue(),
                'amount' => $item->getAmount(),
            ];
        }, $items);
    }

    private function formatMetadata(Metadata $metadata): array
    {
        return [
            'custom_id' => (string)$metadata->getCustomId(),
            'notification_url' => $metadata->getNotificationUrl(),
        ];
    }

    private function formatCustomer(BankingBillet $payment): array
    {
        $customer = [];

        if ($payment->getEnviarEmailParaClienteFinal()) {
            $customer["email"] = $payment->getCustomer()->email;
        }

        if (!empty($payment->getCustomer()->telefone)) {
            $customer["phone_number"] = $payment->getCustomer()->telefone;
        }

        if (!$payment->getCustomer()->isJuridicalPerson()) {
            $customer["name"] = $payment->getCustomer()->nome;
            $customer["cpf"] = $payment->getCustomer()->documento;
        } else {
            $customer["juridical_person"] = [
                "corporate_name" => $payment->getCustomer()->nome,
                "cnpj" => $payment->getCustomer()->documento,
            ];
        }

        return $customer;
    }

    private function formatConfiguration(Configuration $configuration): array
    {
        return [
            'fine' => $configuration->getFine(),
            'interest' => $configuration->getInterest(),
        ];
    }

    private function formatDiscount(Discount $discount): array
    {
        return [
            'type' => $discount->getType(),
            'value' => $discount->getValue(),
        ];
    }
}
