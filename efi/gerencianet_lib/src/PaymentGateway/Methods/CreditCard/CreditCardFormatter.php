<?php

namespace PaymentGateway\Methods\CreditCard;

use PaymentGateway\Logging\TransactionLogger;
use PaymentGateway\Methods\Boleto\Item;
use PaymentGateway\Methods\Interfaces\Metadata;
use PaymentGateway\DTOs\Client\ClientDTO;

class CreditCardFormatter
{
    /**
     * Formata o objeto CreditCardBody para um array.
     *
     * @param CreditCardBody $payment Objeto de pagamento a ser formatado.
     * @return array Estrutura formatada do boleto.
     */
    public function format(CreditCardBody $payment): array
    {
        try {
            $items = $this->formatItems($payment->getItems());
            $metadata = $this->formatMetadata($payment->getMetadata());
            $customer = $this->formatCustomer($payment);

            $creditCard = [
                "customer" => $customer,
                "installments" =>  $payment->getInstallments(),
                "payment_token" => $payment->getPaymentToken()->getValue()
            ];
            return [
                'items' => $items,
                'metadata' => $metadata,
                'payment' => [
                    "credit_card" => $creditCard,
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

    private function formatCustomer(CreditCardBody $payment): array
    {
        $customer = [];



        $customer["phone_number"] = $payment->getCustomer()->telefone;
        $customer["email"] = $payment->getCustomer()->email;

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
}
