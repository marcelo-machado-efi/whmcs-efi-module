<?php

namespace PaymentGateway\Methods\CreditCard;

use PaymentGateway\DTOs\Client\ClientDTO;
use PaymentGateway\Logging\TransactionLogger;
use PaymentGateway\Models\Invoice\InvoiceItem;
use PaymentGateway\Methods\Boleto\ItemManager;
use PaymentGateway\Methods\Interfaces\Metadata;
use PaymentGateway\Methods\CreditCard\CreditCardBody;
use PaymentGateway\Methods\CreditCard\CreditCardFormatter;
use PaymentGateway\Methods\CreditCard\PaymentToken;

class CreditCardConfig
{
    private CreditCardBody $payment;
    private ItemManager $itemManager;
    private CreditCardFormatter $formatter;
    private array $orderAttributes;

    public function __construct($orderAttributes)
    {
        $this->payment = new CreditCardBody();
        $this->itemManager = new ItemManager();
        $this->formatter = new CreditCardFormatter();
        // $this->orderAttributes = $orderAttributes;
        $this->setInstallments($orderAttributes["paramsCartao"]["numParcelas"]);
    }

    private function setInstallments(int $installments): void
    {
        $this->payment->setInstallments($installments);
    }
    /**
     * Adiciona itens ao boleto.
     *
     * @param InvoiceItem[] $items
     */
    public function addItems(array $items): self
    {
        try {
            $this->itemManager->addItems($items, $this->payment);
            return $this;
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return $this;
        }
    }

    public function setMetadata(Metadata $metadata): self
    {
        $this->payment->setMetadata($metadata);
        return $this;
    }

    public function setCustomer(ClientDTO $customer): self
    {
        $this->payment->setCustomer($customer);
        return $this;
    }

    public function setPaymentToken(PaymentToken $paymentToken): self
    {
        $this->payment->setPaymentToken($paymentToken);
        return $this;
    }



    /**
     * Retorna a configuração do boleto formatada como um array.
     *
     * @return array
     */
    public function getConfig(): array
    {
        try {
            $config = $this->formatter->format($this->payment);
            TransactionLogger::log(json_encode($config), TransactionLogger::DEBUG_LOG);

            return $config;
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return [];
        }
    }
}
