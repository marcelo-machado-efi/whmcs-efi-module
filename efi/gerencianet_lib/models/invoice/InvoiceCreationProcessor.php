<?php
require_once realpath(__DIR__ . '/../subscription/billet/BilletProcessorSubscription.php');
require_once realpath(__DIR__ . '/../subscription/card/CardProcessorSubscription.php');
require_once realpath(__DIR__ . '/../subscription/pix/PixProcessorSubscription.php');


use Illuminate\Database\Capsule\Manager as Capsule;

class InvoiceCreationProcessor
{
    /**
     * Método principal que processa o hook de criação de fatura
     * @param array $vars - Variáveis do hook InvoiceCreation do WHMCS
     */
    public function handler(array $vars)
    {
        try {

            // Obtém o invoiceId a partir das variáveis do hook
            $invoiceId = $vars['invoiceid'];

            // Busca todos os relid associados à fatura
            $relIds = $this->getRelIdsFromInvoice($invoiceId);

            // Consulta a tabela tblsubscriptionefi com os relIds
            $subscriptionData = $this->getSubscriptionDataByRelIds($relIds);
            $permitionToPay = $this->verifyPermitionTopay($subscriptionData);

            if ($permitionToPay) {
                switch ($subscriptionData[0]->payment_method) {
                    case 'billet':
                        $subscriptionProcessor = new BilletProcessorSubscription($invoiceId, $subscriptionData);
                        $subscriptionProcessor->generateChargeToInvoice();
                        break;
                    case 'card':
                        $subscriptionProcessor = new CardProcessorSubscription($invoiceId, $subscriptionData);
                        $subscriptionProcessor->generateChargeToInvoice();
                        break;
                    case 'pix':
                        $subscriptionProcessor = new PixProcessorSubscription($invoiceId, $subscriptionData);
                        $subscriptionProcessor->generateChargeToInvoice();
                        break;

                    default:
                        logActivity('Efí: falha ao processar assinatura de fatura: ' . $invoiceId);
                        break;
                }
            }
        } catch (Exception $e) {
            logActivity("Erro ao processar hook de criação de fatura: " . $e->getMessage());
        }
    }
    /**
     * Método que verifica se todos os items utilizam a mesma forma de pagamento
     * @param array $subscriptionData 
     */
    private function verifyPermitionTopay($subscriptionData): bool
    {
        $primeiroValor = $subscriptionData[0]->payment_method ?? null;

        if (empty($subscriptionData)) {
            return false;
        }


        // Percorre o array e compara o valor de cada elemento
        foreach ($subscriptionData as $item) {
            if ($item->payment_method !== $primeiroValor) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtém os relids associados à fatura a partir de tblinvoiceitems
     * @param int $invoiceId
     * @return array - Lista de relids
     */
    private function getRelIdsFromInvoice(int $invoiceId): array
    {
        try {
            return Capsule::table('tblinvoiceitems')
                ->where('invoiceid', $invoiceId)
                ->pluck('relid')
                ->toArray();
        } catch (Exception $e) {
            logActivity("Erro ao buscar relids da fatura: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca os orderIds nas tabelas tblhosting, tbldomain e tblhostingaddons com base nos relids
     * @param array $relIds
     * @return array - Lista de orderIds
     */
    private function getOrderIdsFromRelIds(array $relIds): array
    {
        try {
            $hostingOrders = Capsule::table('tblhosting')
                ->whereIn('id', $relIds)
                ->pluck('orderid')
                ->toArray();

            $domainOrders = Capsule::table('tbldomains')
                ->whereIn('id', $relIds)
                ->pluck('orderid')
                ->toArray();

            $addonOrders = Capsule::table('tblhostingaddons')
                ->whereIn('id', $relIds)
                ->pluck('orderid')
                ->toArray();

            return array_merge($hostingOrders, $domainOrders, $addonOrders);
        } catch (Exception $e) {
            logActivity("Erro ao buscar orderIds com base nos relids: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Consulta a tabela tblsubscriptionefi com os relids filtrados
     * @param array $relIds
     * @return array|null - Dados da tabela tblsubscriptionefi ou null
     */
    private function getSubscriptionDataByRelIds(array $relIds): ?array
    {
        try {
            $subscriptionsList = Capsule::table('tblsubscriptionefi')
                ->whereIn('relid', $relIds)
                ->get()
                ->toArray() ?: null;
            return $subscriptionsList;
        } catch (Exception $e) {
            logActivity("Erro ao consultar tabela tblsubscriptionefi: " . $e->getMessage());
            return 'falha banco de dados';
        }
    }
}
