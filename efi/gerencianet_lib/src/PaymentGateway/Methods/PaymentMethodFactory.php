<?php

namespace PaymentGateway\Methods;

use PaymentGateway\Factories\BoletoPaymentFactory;
use PaymentGateway\Factories\PixPaymentFactory;
use PaymentGateway\Methods\CreditCardPayment;
use PaymentGateway\Methods\PixPayment;
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
                return new CreditCardPayment();
            case 'pix':
                return  PixPaymentFactory::create($orderAttributes);
            case 'open_finance':
                return new OpenFinancePayment();
            default:
                throw new \InvalidArgumentException("Unsupported payment type: $paymentType");
        }
    }
}
