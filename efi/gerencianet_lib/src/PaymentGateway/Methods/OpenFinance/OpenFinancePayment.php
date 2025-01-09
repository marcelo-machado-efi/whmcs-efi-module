<?php

namespace PaymentGateway\Methods\OpenFinance;

use Efi\EfiPay;
use PaymentGateway\Methods\PaymentMethodStrategy;
use PaymentGateway\DTOs\Config\ConfigAPIDTO;
use PaymentGateway\Models\Invoice\WHMCSInvoice;
use PaymentGateway\Database\OpenFinanceDAO;
use PaymentGateway\Logging\TransactionLogger;

class OpenFinancePayment implements PaymentMethodStrategy
{
    private ConfigAPIDTO $configApi;
    private OpenFinanceConfig $openFinanceConfig;
    private WHMCSInvoice $invoice;
    private OpenFinanceDAO $databaseInteraction;
    private Favorecido $favorecido;
    private Pagador $pagador;

    /**
     * Construtor para a classe OpenFinancePayment.
     *
     * @param ConfigAPIDTO $configApi Instância para configuração da API.
     * @param OpenFinanceConfig $openFinanceConfig Configuração específica do Open Finance.
     * @param WHMCSInvoice $invoice Objeto representando a fatura WHMCS.
     * @param OpenFinanceDAO $databaseInteraction Interação com o banco de dados para Open Finance.
     * @param Favorecido $favorecido Configurações do recebedor.
     * @param Pagador $pagador Configurações do Pagador.
     */
    public function __construct(
        ConfigAPIDTO $configApi,
        OpenFinanceConfig $openFinanceConfig,
        WHMCSInvoice $invoice,
        OpenFinanceDAO $databaseInteraction,
        Favorecido $favorecido,
        Pagador $pagador
    ) {
        $this->configApi = $configApi;
        $this->openFinanceConfig = $openFinanceConfig;
        $this->invoice = $invoice;
        $this->databaseInteraction = $databaseInteraction;
        $this->favorecido = $favorecido;
        $this->pagador = $pagador;

        $this->addValuesToOpenFinanceConfig();
    }

    /**
     * Adiciona os valores necessários para configurar o OpenFinance.
     */
    private function addValuesToOpenFinanceConfig(): void
    {
        $this->openFinanceConfig
            ->setFavorecido($this->favorecido)
            ->setPagador($this->pagador)
            ->setValor($this->invoice->getTotal())
            ->setIdProprio($this->invoice->getInvoiceId());
    }
    public function processPayment(): mixed
    {

        try {
            $configApi = $this->configApi->getApiConfig();
            $api = new EfiPay($configApi);
            $params = [];
            $body = $this->openFinanceConfig->getConfig();

            $result = $api->ofStartPixPayment($params, $body);

            return $result;
        } catch (\Throwable $th) {

            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return  "<h1>Falha no pagamento</h1>";
        }
    }

    public function processRefund(string $transactionId): mixed
    {
        // Implementação do estorno via Open Finance
        return true;
    }

    public function setupSubscription(array $subscriptionData): mixed
    {
        // Implementação da assinatura via Open Finance
        return "Open Finance subscription setup for: " . $subscriptionData['id'];
    }
}
