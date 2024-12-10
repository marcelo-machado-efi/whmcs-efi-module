<?php

namespace PaymentGateway\Methods\Pix;

use Efi\EfiPay;
use PaymentGateway\Database\PixDAO;
use PaymentGateway\DTOs\Config\ConfigAPIDTO;
use PaymentGateway\Logging\TransactionLogger;
use PaymentGateway\Methods\Pix\Discount;
use PaymentGateway\Methods\PaymentMethodStrategy;
use PaymentGateway\Methods\Pix\PixConfig;
use PaymentGateway\Models\Invoice\WHMCSInvoice;
use PaymentGateway\Methods\Pix\PixQrCodeGenerator;

class PixPayment implements PaymentMethodStrategy
{
    private ConfigAPIDTO $configConnectionApi;
    private PixConfig $pixConfig;
    private Discount $discount;
    private WHMCSInvoice $invoice;
    private PixDAO $databaseInteraction;

    public function __construct(
        ConfigAPIDTO $configConnectionApi,
        PixConfig $pixConfig,
        Discount $discount,
        WHMCSInvoice $invoice,
        PixDAO $databaseInteraction
    ) {
        $this->configConnectionApi = $configConnectionApi;
        $this->pixConfig = $pixConfig;
        $this->discount = $discount;
        $this->invoice = $invoice;
        $this->databaseInteraction = $databaseInteraction;

        $this->addValuesToPixConfig();
    }

    /**
     * Adiciona os valores necessários para configurar o boleto.
     */
    private function addValuesToPixConfig(): void
    {
        $this->pixConfig
            ->setDiscount($this->discount)
            ->setValor($this->invoice->getTotal());
    }

    public function processPayment(): mixed
    {
        try {
            $configApi = $this->configConnectionApi->getApiConfig();
            $api = new EfiPay($configApi);
            $params = [];
            $body = $this->pixConfig->getConfig();
            $responseApiCreatePix = $api->pixCreateImmediateCharge($params, $body);
            $this->savePixDatabase($responseApiCreatePix);

            $img = $this->getImgPix($responseApiCreatePix, $api);

            return $img;
        } catch (\Throwable $th) {

            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return  "<h1>Falha no pagamento</h1>";
        }
    }
    private function getImgPix($responseApiCreatePix, $apiInstance)
    {
        $locId = $responseApiCreatePix["loc"]["id"];
        $qrCodeGenerator = new PixQrCodeGenerator($locId, $apiInstance);
        $img = $qrCodeGenerator->getImgQrCode();

        return $img;
    }

    private function savePixDatabase($responseApiCreatePix)
    {
        $dataToInsert = [
            'txid' => $responseApiCreatePix["txid"],
            'invoiceid' => $this->invoice->getInvoiceId(),
            'locid' => $responseApiCreatePix["loc"]["id"],
        ];
        $success = $this->databaseInteraction->create($dataToInsert);

        return $success;
    }


    public function processRefund(string $transactionId): bool
    {
        // Implementação do estorno via Pix
        return "Pix refund processed for transaction: " . $transactionId;
    }

    public function setupSubscription(array $subscriptionData): bool
    {
        // Implementação da assinatura via Pix
        return "Pix subscription setup for: " . $subscriptionData['id'];
    }
}
