<?php

namespace PaymentGateway\Methods\CreditCard;

use Efi\EfiPay;
use PaymentGateway\Database\CreditCardDAO;
use PaymentGateway\DTOs\Client\ClientDTO;
use PaymentGateway\DTOs\Config\ConfigAPIDTO;
use PaymentGateway\Logging\TransactionLogger;
use PaymentGateway\Methods\CreditCard\CreditCardConfig;
use PaymentGateway\Methods\PaymentMethodStrategy;
use PaymentGateway\Models\Invoice\WHMCSInvoice;
use PaymentGateway\Methods\Interfaces\Metadata;
use PaymentGateway\Methods\CreditCard\PaymentToken;

class CreditCardPayment implements PaymentMethodStrategy
{
    private ClientDTO $client;
    private CreditCardConfig $cardConfig;
    private ConfigAPIDTO $configConnectionApi;
    private WHMCSInvoice $invoice;
    private CreditCardDAO $databaseInteraction;
    private Metadata $metadata;
    private PaymentToken $paymentToken;

    /**
     * Construtor para inicializar as propriedades da classe.
     *
     * @param ClientDTO $client Cliente associado ao pagamento.
     * @param ConfigAPIDTO $configConnectionApi Configuração da API de conexão.
     * @param CreditCardConfig $cardConfig Configuração do cartão de crédito.
     * @param WHMCSInvoice $invoice Fatura do WHMCS associada ao pagamento.
     * @param CreditCardDAO $databaseInteraction Interação com o banco de dados para o cartão de crédito.
     * @param Metadata $metadata Informações adicionais sobre o pagamento.
     * @param PaymentToken $paymentToken Token do cartão.
     */
    public function __construct(
        ClientDTO $client,
        ConfigAPIDTO $configConnectionApi,
        CreditCardConfig $cardConfig,
        WHMCSInvoice $invoice,
        CreditCardDAO $databaseInteraction,
        Metadata $metadata,
        PaymentToken $paymentToken
    ) {
        $this->client = $client;
        $this->configConnectionApi = $configConnectionApi;
        $this->cardConfig = $cardConfig;
        $this->invoice = $invoice;
        $this->databaseInteraction = $databaseInteraction;
        $this->metadata = $metadata;
        $this->paymentToken = $paymentToken;

        $this->addValuesToCreditCardConfig();
    }

    /**
     * Adiciona os valores necessários para configurar a cobrança de cartão de crédito.
     */
    private function addValuesToCreditCardConfig(): void
    {
        $this->cardConfig
            ->setMetadata($this->metadata) // Configura o metadata
            ->setCustomer($this->client)
            ->addItems($this->invoice->getItems())
            ->setPaymentToken($this->paymentToken);
    }

    public function processPayment(): mixed
    {

        try {
            $configApi = $this->configConnectionApi->getApiConfig();
            $api = new EfiPay($configApi);

            $params = [];
            $body = $this->cardConfig->getConfig();
            $responseApiCreateCard = $api->createOneStepCharge($params, $body);
            $success = isset($responseApiCreateCard['data']['status']) && $responseApiCreateCard['data']['status'] === 'approved';
            if ($success) {
                $chargeId = $responseApiCreateCard["data"]["charge_id"];
                $success = $this->addTransactionInvoice($chargeId, $this->invoice->getInvoiceId());

                $success = $this->addInvoicePayment($chargeId, $this->invoice->getInvoiceId());
                if ($success) {
                    return $this->generateResult('approved');
                }
            } else {
                return $this->generateResult();
            }
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return false;
        }
        return true;
    }

    private function addTransactionInvoice(string $chargeId, int $invoiceId): bool
    {
        try {
            $command = "AddTransaction";
            $dataToAdd = [
                'paymentmethod' => 'efi',
                'transid' => $chargeId,
                'date' => date('d/m/Y'),
                'invoiceid' => $invoiceId
            ];
            $results = localAPI($command, $dataToAdd);
            return $results['result'] == 'success';
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return false;
        }
    }
    private function addInvoicePayment(string $chargeId, int $invoiceId): bool
    {
        try {
            $totalPago = $this->invoice->getTotal();
            addInvoicePayment($invoiceId, $chargeId, $totalPago, '0.00', 'efi');

            return true;
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return false;
        }
    }

    private function generateResult($status = null)

    {
        if ($status == "approved") {
            return '
                <div class="alert alert-success" role="alert">

                    <h4 class="alert-heading">Pagamento Aprovado</h4>

                    <hr>

                    <p class="mb-0">Seu pagamento foi aprovado! Em alguns segundos a sua fatura será atualizada</p>

                </div>
                <script>
                    setTimeout(function() {
                        window.location.reload();
                    }, 4000);
                  
                </script>

            ';
        }

        return '

        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Pagamento Recusado</h4>
            <hr>
            <p class="mb-0">Seu pagamento foi recusado. Por favor, revise os dados do cartão e tente novamente. Caso o problema persista, entre em contato com a administradora do cartão.</p>
        </div>
                     


        ';
    }

    public function processRefund(string $transactionId): mixed
    {
        // Implementação do estorno via cartão de crédito
        return "Credit card refund processed for transaction: " . $transactionId;
    }

    public function setupSubscription(array $subscriptionData): mixed
    {
        // Implementação da assinatura via cartão de crédito
        return "Credit card subscription setup for: " . $subscriptionData['id'];
    }
}
