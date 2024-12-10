<?php

namespace PaymentGateway\Methods\CreditCard;

use PaymentGateway\DTOs\Client\ClientDTO;
use PaymentGateway\Methods\Boleto\Item;
use PaymentGateway\Methods\Boleto\Metadata;

/**
 * Class BankingBillet
 */
class CreditCardBody
{
    /**
     * @var Item[]
     */
    private array $items = [];

    private ?Metadata $metadata = null;

    private ?ClientDTO $customer = null;


    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }

    public function setMetadata(Metadata $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function setCustomer(ClientDTO $customer): void
    {
        $this->customer = $customer;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getMetadata(): ?Metadata
    {
        return $this->metadata;
    }


    public function getCustomer(): ?ClientDTO
    {
        return $this->customer;
    }
}
