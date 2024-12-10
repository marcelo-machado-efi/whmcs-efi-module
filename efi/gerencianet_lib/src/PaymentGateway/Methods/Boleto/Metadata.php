<?php

namespace PaymentGateway\Methods\Boleto;

/**
 * Class Metadata
 */
class Metadata
{
    private array $gatewayParams;

    /**
     * Metadata constructor.
     * 
     * @param array $gatewayParams
     */
    public function __construct(array $gatewayParams)
    {
        $this->gatewayParams = $gatewayParams;
    }

    public function getCustomId(): string
    {
        $customId = $this->gatewayParams['invoiceid'];
        return $customId;
    }

    public function getNotificationUrl(): string
    {
        $notificationUrl = $this->gatewayParams['systemurl'] . "modules/gateways/callback/efi/billet.php";
        return $notificationUrl;
    }
}
