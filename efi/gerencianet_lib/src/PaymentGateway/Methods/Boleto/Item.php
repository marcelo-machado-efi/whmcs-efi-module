<?php

namespace PaymentGateway\Methods\Boleto;

/**
 * Class Item
 */
class Item
{
    private string $name;
    private float $value;
    private float $amount;

    /**
     * Item constructor.
     * 
     * @param string $name
     * @param float $value
     * @param float $amount
     */
    public function __construct(string $name, float $value, float $amount)
    {
        $this->name = $name;
        $this->value = $value;
        $this->amount = $amount;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): float
    {
        $itemValue = number_format((float)$this->value, 2, '.', '');

        $itemValue = preg_replace("/[.,-]/", "", $itemValue);
        return $itemValue;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
