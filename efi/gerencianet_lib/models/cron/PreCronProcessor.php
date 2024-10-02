<?php
require_once realpath(__DIR__ . '/../subscription/card/CardProcessorSubscription.php');

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;

class PreCronProcessor
{
    /**
     * Método principal que processa o hook de pre cron do WHMCS
     * @param array $vars - Variáveis do hook PreCronJob do WHMCS
     */
    public function handler(array $vars)
    {
        try {
            

            $payments = $this->getTodayPayments();
            
            // Consulta a tabela tblsubscriptionefi com os relIds
            
            foreach ($payments as $payment) {
                $subscriptionData = $this->getSubscriptionDataByRelId($payment->relid);
                
                $subscriptionProcessor = new CardProcessorSubscription($payment->invoiceid, $subscriptionData);
               
                $subscriptionProcessor->generateChargeToInvoice();
            }
        } catch (Exception $e) {
            logActivity("Erro ao processar hook de pre cron : " . $e->getMessage());
        }
    }


    /**
     * Consulta a tabela tblsubscriptionefi com os relids filtrados
     * @param int $relId
     * @return array|null - Dados da tabela tblsubscriptionefi ou null
     */
    private function getSubscriptionDataByRelId(int $relId): ?array
    {
        try {
            $relIds = [$relId];
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

    /**
     * Busca pagamentos agendados para o dia de hoje.
     * 
     * @return array Lista de pagamentos do dia atual.
     */
    private function getTodayPayments(): array
    {
        $today = Carbon::now()->toDateString(); // Obtém a data atual

        // Query para buscar os pagamentos agendados para o dia de hoje
        return Capsule::table('tblschedulepaymentefi')
            ->whereDate('date', '=', $today)
            ->get()
            ->toArray();
    }

    /**
     * Retorna uma lista de 'relid' dos pagamentos agendados para o dia de hoje.
     * 
     * @return array Lista de 'relid' dos pagamentos do dia atual.
     */
    public function getTodayRelids(): array
    {
        // Obtém os pagamentos agendados para hoje
        $todayPayments = $this->getTodayPayments();

        // Percorre o array de pagamentos e retorna somente os 'relid'
        $relids = array_map(function ($payment) {
            return $payment->relid; // Acessa o campo 'relid' do objeto
        }, $todayPayments);

        return $relids; // Retorna a lista de 'relid'
    }
}
