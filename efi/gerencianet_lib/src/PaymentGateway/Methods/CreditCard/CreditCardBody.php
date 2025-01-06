<?php

namespace PaymentGateway\Methods\CreditCard;

use PaymentGateway\DTOs\Client\ClientDTO;
use PaymentGateway\Methods\Base\Charges;
use PaymentGateway\Methods\Boleto\Item;
use PaymentGateway\Methods\Interfaces\Metadata;

/**
 * Class CreditCardBody
 */
class CreditCardBody extends Charges
{
    /**
     * @var Item[]
     */
    private array $items = [];

    private ?Metadata $metadata = null;

    private ?ClientDTO $customer = null;

    private ?PaymentToken $paymentToken = null;

    private ?int $installments = null;

    public function setInstallments(int $installments): void
    {
        $this->installments = $installments;
    }
    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setMetadata(?Metadata $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getMetadata(): ?Metadata
    {
        return $this->metadata;
    }

    public function getInstallments(): ?int
    {
        return $this->installments;
    }

    public function setCustomer(?ClientDTO $customer): void
    {
        $this->customer = $customer;
    }

    public function getCustomer(): ?ClientDTO
    {
        return $this->customer;
    }

    public function setPaymentToken(?PaymentToken $paymentToken): void
    {
        $this->paymentToken = $paymentToken;
    }

    public function getPaymentToken(): ?PaymentToken
    {
        return $this->paymentToken;
    }
}
