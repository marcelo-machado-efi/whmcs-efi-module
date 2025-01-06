<?php

namespace PaymentGateway\Methods\Boleto;

use PaymentGateway\Methods\PaymentMethodStrategy;
use PaymentGateway\Validators\Boleto\BoletoValidation;
use PaymentGateway\DTOs\Client\ClientDTO;
use PaymentGateway\DTOs\Config\ConfigAPIDTO;
use PaymentGateway\Logging\TransactionLogger;
use PaymentGateway\Models\Invoice\WHMCSInvoice;
use PaymentGateway\Database\BoletoDAO;
use Efi\EfiPay;
use PaymentGateway\Methods\Interfaces\Metadata;

class BoletoPayment implements PaymentMethodStrategy
{
    private ClientDTO $client;
    private ConfigAPIDTO $configConnectionApi;
    private BoletoConfig $boletoConfig;
    private Metadata $metadata;
    private Discount $discount;
    private Configuration $configuration;
    private WHMCSInvoice $invoice;
    private BoletoDAO $databaseInteraction;

    /**
     *
     * @param ClientDTO $client
     * @param ConfigAPIDTO $configConnectionApi
     * @param BoletoConfig $boletoConfig
     * @param Metadata $metadata
     * @param Discount $discount
     * @param Configuration $configuration
     * @param WHMCSInvoice $invoice
     * @param BoletoDAO $databaseInteraction
     */
    public function __construct(
        ClientDTO $client,
        ConfigAPIDTO $configConnectionApi,
        BoletoConfig $boletoConfig,
        Metadata $metadata,
        Discount $discount,
        Configuration $configuration,
        WHMCSInvoice $invoice,
        BoletoDAO $databaseInteraction

    ) {
        $this->client = $client;
        $this->configConnectionApi = $configConnectionApi;
        $this->boletoConfig = $boletoConfig;
        $this->metadata = $metadata;
        $this->discount = $discount;
        $this->configuration = $configuration;
        $this->invoice = $invoice;
        $this->databaseInteraction = $databaseInteraction;

        $this->addValuesToBoletoConfig();
    }

    /**
     * Adiciona os valores necessários para configurar o boleto.
     */
    private function addValuesToBoletoConfig(): void
    {
        $this->boletoConfig
            ->setMetadata($this->metadata)
            ->setExpireAt($this->invoice->getDueDate())
            ->setConfiguration($this->configuration)
            ->setDiscount($this->discount)
            ->setCustomer($this->client)
            ->addItems($this->invoice->getItems());
    }

    /**
     * Valida os dados do boleto.
     *
     * @return array
     */
    private function validate(): array
    {
        try {
            $validation = new BoletoValidation($this->client);
            return $validation->validate();
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return [];
        }
    }


    public function processPayment(): mixed
    {
        try {
            $msgErros = $this->validate();
            $configApi = $this->configConnectionApi->getApiConfig();

            if (empty($msgErros)) {
                $api = new EfiPay($configApi);
                $params = [];
                $body = $this->boletoConfig->getConfig();
                $responseApiCreateBillet = $api->createOneStepCharge($params, $body);
                $chargeId = $responseApiCreateBillet["data"]["charge_id"];
                $success = $this->addTransactionInvoice($chargeId, $this->invoice->getInvoiceId());
                $success = $this->saveBoletoDatabase($responseApiCreateBillet);
                return $success;
            }

            return false;
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return false;
        }
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

    private function saveBoletoDatabase($boletoData): bool
    {
        $dataToInsert = [
            'charge_id' => $boletoData["data"]["charge_id"],
            'invoiceid' => $this->invoice->getInvoiceId(),
            'link_pdf' => $boletoData["data"]["pdf"]["charge"],
        ];

        $success = $this->databaseInteraction->create($dataToInsert);

        return $success;
    }

    public function processRefund(string $transactionId): bool
    {
        // Implementação do estorno via boleto
        return "Boleto refund processed for transaction: " . $transactionId;
    }

    public function setupSubscription(array $subscriptionData): bool
    {
        // Implementação da assinatura via boleto
        return "Boleto subscription setup for: " . $subscriptionData['id'];
    }
}
