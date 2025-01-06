<?php

namespace PaymentGateway\Methods\Boleto;

use PaymentGateway\Methods\Interfaces\Metadata;

/**
 * Class MetadataBillet
 */
class MetadataBillet implements Metadata
{
    private array $gatewayParams;

    /**
     * MetadataBillet constructor.
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
