<?php

namespace PaymentGateway\Methods\Boleto;

/**
 * Class Interest
 */
class Configuration
{
    private int $fine, $interest;

    /**
     * Interest constructor.
     * 
     * @param array $gatewayParams
     * @throws \InvalidArgumentException
     */
    public function __construct(array $gatewayParams)
    {

        $this->fine = (int) ($gatewayParams['fineValue']);
        $this->interest = (int) $gatewayParams['interestValue'];
    }

    public function getFine(): int
    {
        return $this->fine;
    }

    public function getInterest(): int
    {
        return $this->interest;
    }
}
