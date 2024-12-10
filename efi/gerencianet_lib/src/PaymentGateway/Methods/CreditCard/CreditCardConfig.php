<?php

namespace PaymentGateway\Methods\Boleto;

use PaymentGateway\DTOs\Client\ClientDTO;
use PaymentGateway\Logging\TransactionLogger;
use PaymentGateway\Models\Invoice\InvoiceItem;
use DateInterval;
use DateTime;
use PaymentGateway\Methods\CreditCard\CreditCardBody;

class BoletoConfig
{
    private CreditCardBody $payment;
    private ItemManager $itemManager;
    private BoletoFormatter $formatter;
    private array $orderAttributes;

    public function __construct($orderAttributes)
    {
        $this->payment = new CreditCardBody();
        $this->itemManager = new ItemManager();
        $this->formatter = new BoletoFormatter();
        $this->orderAttributes = $orderAttributes;
        $this->setMessage($orderAttributes['message']);
        $this->setEnviarEmailParaClienteFinal($orderAttributes['sendEmailGN'] == 'on');
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

    public function setExpireAt(string $expireAt): self
    {
        $numDiasParaVencimento = $this->orderAttributes['numDiasParaVencimento'];
        if ($numDiasParaVencimento == null || $numDiasParaVencimento == '')  $numDiasParaVencimento = '0';

        $date = DateTime::createFromFormat('Y-m-d', $expireAt);

        $date->add(new DateInterval('P' . (string)$numDiasParaVencimento . 'D'));
        $dueDateBillet = (string)$date->format('Y-m-d');

        $this->payment->setExpireAt($dueDateBillet);
        return $this;
    }

    public function setDiscount(Discount $discount): self
    {


        $this->payment->setDiscount($discount);
        return $this;
    }

    public function setConfiguration(Configuration $configuration): self
    {
        $this->payment->setConfiguration($configuration);
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->payment->setMessage($message);
        return $this;
    }

    public function setCustomer(ClientDTO $customer): self
    {
        $this->payment->setCustomer($customer);
        return $this;
    }

    public function setEnviarEmailParaClienteFinal(bool $enviarEmailParaClienteFinal): self
    {
        $this->payment->setEnviarEmailParaClienteFinal($enviarEmailParaClienteFinal);
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
