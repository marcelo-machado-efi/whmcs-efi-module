<?php

require_once realpath(__DIR__ . '/../../../functions/gateway/CreditCard.php');
require_once realpath(__DIR__ . '/../../../../../../../init.php');
require_once realpath(__DIR__ . '/../../../GerencianetIntegration.php');
require_once realpath(__DIR__ . '/../database/SubscriptionEfiDataBase.php');

use WHMCS\Config\Setting;
use WHMCS\Billing\Invoice;
use Carbon\Carbon;



/**
 * Class CardProcessorSubscription
 * 
 * Esta classe é responsável por processar assinaturas e cobranças de cartão relacionados a faturas.
 */
class CardProcessorSubscription
{
    /**
     * @var Invoice   fatura do WHMCS
     */
    private $invoice;
    /**
     * @var EfiSubscriptionDatabase   fatura do WHMCS
     */
    private $efiSubscriptionDatabase;

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
        $this->efiSubscriptionDatabase = new EfiSubscriptionDatabase();
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
        $params['paramsCartao'] = json_decode($this->subscriptionData[0]->customer, true);
        $params['systemurl'] = Setting::getValue('SystemURL');
        $params['amount'] = $this->invoice->total;
        
        if ($this->verifyPaymentSchedule()) {
            $existingChargeConfirm = existingChargeCredit($params, $this->gnIntegration);
            
            $existingCharge = $existingChargeConfirm['existCharge'];

            

            if (!$existingCharge) {
                
                createCard($params, $this->gnIntegration, [], false);
            }
        } else {
           
            $this->createPaymentSchedule();
        }
    }
    /**
     * Função que verifica se o pagamento da fatura deve ocorrer hoje ou no vencimento
     */
    private function verifyPaymentSchedule(): bool
    {
        $permitionDate = Carbon::parse($this->invoice->duedate)->isToday();  
        
        $permitionStatus = ($this->invoice->status != 'Paid'); // Verifica se a fatura não foi paga
        
 
        return ($permitionDate && $permitionStatus);
    }

    /**
     * Função que agenda  pagamento de cartão  para o futuro
     */
    private function createPaymentSchedule(): bool
    {
        try {
            $permitionStatus = ($this->invoice->status != 'Paid');
            $havePayment = $this->efiSubscriptionDatabase->getScheduledPaymentByInvoiceId($this->invoice->id);
            if ($permitionStatus && !$havePayment ) {
                $customer = json_decode($this->subscriptionData[0]->customer, true);
                foreach ($this->subscriptionData as $subscription) {
                    $this->efiSubscriptionDatabase->addScheduledPayment($this->invoice->id, $customer['payment_token'], $this->invoice->duedate, $subscription->relid);
                }
            }
            return $permitionStatus;
        } catch (\Throwable $th) {
            logActivity('Falha ao  agendar pagamento de fatura:' . $this->invoice->id);
            return false;
        }
    }
}
