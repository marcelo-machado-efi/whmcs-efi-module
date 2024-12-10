<?php

namespace PaymentGateway\Methods\Boleto;

use PaymentGateway\DTOs\Client\ClientDTO;
use PaymentGateway\Methods\Base\Charges;
use PaymentGateway\Methods\Boleto\Item;


/**
 * Class BankingBillet
 */
class BankingBillet extends Charges
{
    /**
     * @var Item[]
     */
    private array $items = [];

    private ?Metadata $metadata = null;

    private ?string $expireAt = null;

    private ?Discount $discount = null;

    private ?Configuration $configuration = null;

    private ?ClientDTO $customer = null;

    private ?string $message = null;
    private ?bool $enviarEmailParaClienteFinal = null;

    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }

    public function setMetadata(Metadata $metadata): void
    {
        $this->metadata = $metadata;
    }
    public function setEnviarEmailParaClienteFinal(bool $enviarEmailParaClienteFinal): void
    {
        $this->enviarEmailParaClienteFinal = $enviarEmailParaClienteFinal;
    }
    public function setCustomer(ClientDTO $customer): void
    {
        $this->customer = $customer;
    }

    public function setExpireAt(string $expireAt): void
    {
        $this->expireAt = $expireAt;
    }

    public function setDiscount(Discount $discount): void
    {
        $this->discount = $discount;
    }

    public function setConfiguration(Configuration $configuration): void
    {
        $this->configuration = $configuration;
    }



    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getMetadata(): ?Metadata
    {
        return $this->metadata;
    }
    public function getExpireAt(): ?string
    {
        return $this->expireAt;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }


    public function getCustomer(): ?ClientDTO
    {
        return $this->customer;
    }
    public function getEnviarEmailParaClienteFinal(): ?bool
    {
        return $this->enviarEmailParaClienteFinal;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
