<?php

namespace PaymentGateway\Methods\CreditCard;

use PaymentGateway\Methods\Interfaces\Metadata;

/**
 * Class MetadataCreditCard
 */
class MetadataCreditCard implements Metadata
{
    private array $gatewayParams;

    /**
     * MetadataCreditCard constructor.
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
        $notificationUrl = $this->gatewayParams['systemurl'] . "modules/gateways/callback/efi/card.php";
        return $notificationUrl;
    }
}
