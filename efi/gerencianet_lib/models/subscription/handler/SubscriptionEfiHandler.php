<?php

require_once realpath(__DIR__ . '/../database/SubscriptionEfiDataBase.php');


use WHMCS\Billing\Invoice;



use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\QueryException;
use Exception;

/**
 * Class SubscriptionEfiHandler
 *
 * Modelo para manipular assinaturas no WHMCS.
 */
class SubscriptionEfiHandler
{
    /**
     * Cria uma nova assinatura.
     *
     * @param string $paymentMethod Método de pagamento
     * @param int $relId identificador úinico da recorrência
     * @param array $customer cliente  do pedido
     * @return bool Retorna true se a assinatura for criada com sucesso, false caso contrário
     * @throws Exception Se ocorrer um erro ao criar a assinatura
     */
    public function createSubscription(string $paymentMethod, int $relId, array $customer ): bool
    {
        try {

            
            $result = EfiSubscriptionDatabase::addSubscription($paymentMethod, $relId, $customer);
            logActivity("Assinatura criada com sucesso para o rel ID: $relId.");
            return $result;
        } catch (QueryException $e) {
            logActivity('Erro ao criar assinatura: ' . $e->getMessage());
            throw new Exception('Erro ao criar assinatura: ' . $e->getMessage(), (int) $e->getCode(), $e);
        } catch (Exception $e) {
            logActivity('Erro inesperado ao criar assinatura: ' . $e->getMessage());
            throw new Exception('Erro inesperado ao criar assinatura: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }
    /**
     * Cria uma nova assinatura.
     *
     * @param int $invoiceId Numero da fatura
     * @param string $paymentToken
     * @param int $relId 
     * @return bool Retorna true se a assinatura for criada com sucesso, false caso contrário
     * @throws Exception Se ocorrer um erro ao criar a assinatura
     */
    public function createSchedulePayment(int $invoiceId, string $paymentToken, int $relId  ): bool
    {
        try {

            $invoice = Invoice::find($invoiceId);
            $result = EfiSubscriptionDatabase::addScheduledPayment($invoiceId, $paymentToken, $invoice->dueDate,$relId );
            logActivity("Pagamento agendado com sucesso para o rel ID: $relId.");
            return $result;
        } catch (QueryException $e) {
            logActivity('Erro ao agendar pagamento: ' . $e->getMessage());
            throw new Exception('Erro ao agendar pagamento: ' . $e->getMessage(), (int) $e->getCode(), $e);
        } catch (Exception $e) {
            logActivity('Erro inesperado ao agendar pagamento: ' . $e->getMessage());
            throw new Exception('Erro inesperado ao criar assinatura: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Obtém uma assinatura pelo ID do pedido.
     *
     * @param int $orderId ID do pedido
     * @return object|null Retorna a assinatura se encontrada, null caso contrário
     * @throws Exception Se ocorrer um erro ao obter a assinatura
     */
    public function findSubscriptionByOrderId(int $orderId): ?object
    {
        try {
            $subscription = EfiSubscriptionDatabase::getSubscriptionByRelId($orderId);
            logActivity("Assinatura obtida com sucesso para o pedido ID: $orderId.");
            return $subscription;
        } catch (QueryException $e) {
            logActivity('Erro ao obter assinatura: ' . $e->getMessage());
            throw new Exception('Erro ao obter assinatura: ' . $e->getMessage(), (int) $e->getCode(), $e);
        } catch (Exception $e) {
            logActivity('Erro inesperado ao obter assinatura: ' . $e->getMessage());
            throw new Exception('Erro inesperado ao obter assinatura: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Atualiza o status de uma assinatura existente.
     *
     * @param int $orderId ID do pedido
     * @param string $status Novo status da assinatura
     * @return bool Retorna true se o status for atualizado com sucesso, false caso contrário
     * @throws Exception Se ocorrer um erro ao atualizar o status
     */
    public function updateSubscriptionStatus(int $orderId, string $status): bool
    {
        try {
            $result = EfiSubscriptionDatabase::updateSubscriptionStatus($orderId, $status);
            logActivity("Status da assinatura atualizado com sucesso para o pedido ID: $orderId.");
            return $result;
        } catch (QueryException $e) {
            logActivity('Erro ao atualizar o status da assinatura: ' . $e->getMessage());
            throw new Exception('Erro ao atualizar o status da assinatura: ' . $e->getMessage(), (int) $e->getCode(), $e);
        } catch (Exception $e) {
            logActivity('Erro inesperado ao atualizar o status da assinatura: ' . $e->getMessage());
            throw new Exception('Erro inesperado ao atualizar o status da assinatura: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Remove uma assinatura existente.
     *
     * @param int $orderId ID do pedido
     * @return bool Retorna true se a assinatura for removida com sucesso, false caso contrário
     * @throws Exception Se ocorrer um erro ao remover a assinatura
     */
    public function deleteSubscription(int $orderId): bool
    {
        try {
            $result = EfiSubscriptionDatabase::deleteSubscription($orderId);
            logActivity("Assinatura removida com sucesso para o pedido ID: $orderId.");
            return $result;
        } catch (QueryException $e) {
            logActivity('Erro ao remover assinatura: ' . $e->getMessage());
            throw new Exception('Erro ao remover assinatura: ' . $e->getMessage(), (int) $e->getCode(), $e);
        } catch (Exception $e) {
            logActivity('Erro inesperado ao remover assinatura: ' . $e->getMessage());
            throw new Exception('Erro inesperado ao remover assinatura: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    private function getOrderIdByInvoiceId($invoiceId)
    {
        try {
            // Consultar o banco de dados para obter o orderid com base no invoiceid
            $orderId = Capsule::table('tblinvoices')
                ->join('tblorders', 'tblinvoices.id', '=', 'tblorders.invoiceid')
                ->where('tblinvoices.id', $invoiceId)
                ->value('tblorders.id');

            if ($orderId === null) {
                throw new Exception('Order ID não encontrado para o Invoice ID: ' . $invoiceId);
            }

            return $orderId;
        } catch (\Exception $e) {
            if (is_int($e->getCode())) {
                throw new Exception('Erro ao recuperar o Order ID: ' . $e->getMessage(), $e->getCode(), $e);
            } else {
                throw new Exception('Erro ao recuperar o Order ID: ' . $e->getMessage(), 0, $e);
            }
        }
    }
}
