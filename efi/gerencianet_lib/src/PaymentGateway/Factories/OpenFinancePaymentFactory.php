<?php

namespace PaymentGateway\Factories;

use PaymentGateway\Methods\PaymentMethodStrategy;
use PaymentGateway\DTOs\Config\ConfigAPIDTO;
use PaymentGateway\Models\Invoice\WHMCSInvoice;
use PaymentGateway\Database\OpenFinanceDAO;
use PaymentGateway\Methods\OpenFinance\OpenFinanceConfig;
use PaymentGateway\Methods\OpenFinance\OpenFinancePayment;
use PaymentGateway\Methods\OpenFinance\Favorecido;
use PaymentGateway\Methods\OpenFinance\ContaBanco;
use PaymentGateway\Methods\OpenFinance\Pagador;

class OpenFinancePaymentFactory
{
    public static function create(array $orderAttributes): PaymentMethodStrategy
    {
        $configApi = new ConfigAPIDTO($orderAttributes);
        $invoice = new WHMCSInvoice($orderAttributes['invoiceid']);
        $openFinanceConfig = new OpenFinanceConfig($orderAttributes);
        $databaseInteraction = new OpenFinanceDAO();
        $contaBanco = new ContaBanco($orderAttributes);
        $favorecido = new Favorecido($contaBanco);
        $pagador = new Pagador($orderAttributes);

        return new OpenFinancePayment(
            $configApi,
            $openFinanceConfig,
            $invoice,
            $databaseInteraction,
            $favorecido,
            $pagador
        );
    }
}
