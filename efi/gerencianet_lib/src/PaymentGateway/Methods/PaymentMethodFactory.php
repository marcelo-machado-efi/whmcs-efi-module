<?php

namespace PaymentGateway\Methods;

use PaymentGateway\Factories\BoletoPaymentFactory;
use PaymentGateway\Factories\CreditCardPaymentFactory;
use PaymentGateway\Factories\OpenFinancePaymentFactory;
use PaymentGateway\Factories\PixPaymentFactory;
use PaymentGateway\Methods\OpenFinancePayment;
use PaymentGateway\Methods\PaymentMethodStrategy;

class PaymentMethodFactory
{
    public static function create(string $paymentType, array $orderAttributes = []): PaymentMethodStrategy
    {
        switch ($paymentType) {
            case 'boleto':
                return BoletoPaymentFactory::create($orderAttributes);
            case 'credit_card':
                return CreditCardPaymentFactory::create($orderAttributes);
            case 'pix':
                return  PixPaymentFactory::create($orderAttributes);
            case 'open_finance':
                return  OpenFinancePaymentFactory::create($orderAttributes);
            default:
                throw new \InvalidArgumentException("Unsupported payment type: $paymentType");
        }
    }
}
