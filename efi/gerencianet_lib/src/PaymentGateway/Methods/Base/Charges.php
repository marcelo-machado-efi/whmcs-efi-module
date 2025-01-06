<?php

namespace PaymentGateway\Methods\Base;

use PaymentGateway\Methods\Boleto\Item;
use PaymentGateway\Methods\Interfaces\Metadata;

abstract class Charges
{
    /**
     * @var Item[]
     */
    private array $items = [];

    private ?Metadata $metadata;

    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }
    public function getItems(): array
    {
        return $this->items;
    }
}
