<?php

namespace PaymentGateway\Methods\Interfaces;

/**
 * Interface Metadata
 */
interface Metadata
{

    public function getCustomId(): string;
    public function getNotificationUrl(): string;
}
