<?php

require_once realpath(__DIR__ . '/../../../functions/gateway/Billet.php');
require_once realpath(__DIR__ . '/../../../../../../../init.php');
require_once realpath(__DIR__ . '/../../../GerencianetIntegration.php');
use WHMCS\Config\Setting;



/**
 * Class BilletProcessorSubscription
 * 
 * Esta classe é responsável por processar assinaturas e boletos relacionados a faturas.
 */
class BilletProcessorSubscription
{
    /**
     * @var int ID da fatura
     */
    private $invoiceId;

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
        $this->invoiceId = $invoiceId;
        $this->subscriptionData = $subscriptionData;
        $this->gatewayParams = getGatewayVariables('efi');
        $this->gnIntegration = new GerencianetIntegration($this->gatewayParams['clientIdProd'], $this->gatewayParams['clientSecretProd'], $this->gatewayParams['clientIdSandbox'], $this->gatewayParams['clientSecretSandbox'], $this->gatewayParams['sandbox'], $this->gatewayParams['idConta']);
    }

    /**
     * Função responsável por gerar a cobrança referente a fatura do cliente
     * 
     */
    public function generateChargeToInvoice() : void {
        $params = [];
        $params = $this->gatewayParams;
        $params['invoiceid'] = $this->invoiceId;
        $params['paramsBoleto'] = json_decode($this->subscriptionData[0]->customer, true);
        $params['systemurl'] = Setting::getValue('SystemURL');
        $existingChargeConfirm = existingCharge($params, $this->gnIntegration);

        $existingCharge = $existingChargeConfirm['existCharge'];
    
        
       
    
        if ((!$existingCharge) && ($existingCharge != null)) {
            createBillet($params,$this->gnIntegration,[],false);
        }
        
        
    }
}