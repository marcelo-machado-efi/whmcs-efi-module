<?php

namespace PaymentGateway\Methods\Base;

use PaymentGateway\Methods\Boleto\Item;
use PaymentGateway\Methods\Boleto\Metadata;

abstract class Charges
{
    /**
     * @var Item[]
     */
    private array $items;

    private ?Metadata $metadata;
}
