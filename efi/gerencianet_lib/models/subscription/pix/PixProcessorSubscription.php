<?php

require_once realpath(__DIR__ . '/../../../functions/gateway/PIX.php');
require_once realpath(__DIR__ . '/../../../../../../../init.php');
require_once realpath(__DIR__ . '/../../../GerencianetIntegration.php');

use WHMCS\Config\Setting;
use WHMCS\Billing\Invoice;


/**
 * Class PixProcessorSubscription
 * 
 * Esta classe é responsável por processar assinaturas pix relacionados a faturas.
 */
class PixProcessorSubscription
{
    /**
     * @var Invoice Fatura do whmcs
     */
    private $invoice;

    /**
     * @var array Dados da assinatura recuperado da tabela tblsubscriptionefi
     */
    private $subscriptionData;

    /**
     * @var array Dados referentes ao gateway
     */
    private $gatewayParams;

    /**
     * @var GerencianetIntegration 
     */
    private $gnIntegration;

    /**
     * BilletProcessorSubscription constructor.
     * 
     * @param int $invoiceId ID da fatura
     * @param array $subscriptionData Dados da assinatura
     */
    public function __construct(int $invoiceId, array $subscriptionData)
    {
        $this->invoice = Invoice::find($invoiceId);
        $this->subscriptionData = $subscriptionData;
        $this->gatewayParams = getGatewayVariables('efi');
        $this->gnIntegration = new GerencianetIntegration($this->gatewayParams['clientIdProd'], $this->gatewayParams['clientSecretProd'], $this->gatewayParams['clientIdSandbox'], $this->gatewayParams['clientSecretSandbox'], $this->gatewayParams['sandbox'], $this->gatewayParams['idConta']);
    }

    /**
     * Função responsável por gerar a cobrança referente a fatura do cliente
     * 
     */
    public function generateChargeToInvoice(): void
    {
        $params = [];
        $params = $this->gatewayParams;
        $params['invoiceid'] = $this->invoice->id;
        $params['paramsPix'] = json_decode($this->subscriptionData[0]->customer, true);
        $params['systemurl'] = Setting::getValue('SystemURL');
        $params['companyname'] = Setting::getValue('CompanyName');
        $params['amount'] = $this->invoice->total;

        // Verifying if exists a Pix Charge for current invoiceId

        $existingPixCharge = getPixCharge($this->invoice->id);



        if (empty($existingPixCharge)) {

            // Creating a new Pix Charge

            $newPixCharge = createPixCharge($this->gnIntegration, $params);



            if (isset($newPixCharge['txid'])) {

                // Storing Pix Charge Infos on table 'tblgerencianetpix' for later use

                storePixChargeInfo($newPixCharge, $params);
            }
        }
    }
}
