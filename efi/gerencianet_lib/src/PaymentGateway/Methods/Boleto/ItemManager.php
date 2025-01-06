<?php

namespace PaymentGateway\Methods\Boleto;

use PaymentGateway\Models\Invoice\InvoiceItem;
use PaymentGateway\Logging\TransactionLogger;
use PaymentGateway\Methods\Base\Charges;
use PaymentGateway\Methods\Boleto\Item;

class ItemManager
{
    /**
     * Adiciona uma lista de itens ao objeto BankingBillet.
     *
     * @param InvoiceItem[] $items Lista de itens da fatura.
     * @param Charges $payment Objeto de pagamento onde os itens serão adicionados.
     * @throws \InvalidArgumentException Se algum item não for uma instância válida.
     */
    public function addItems(array $items, Charges $payment): void
    {
        try {
            foreach ($items as $item) {


                $name = $item->getDescription();
                $value = (float)$item->getAmount();
                $amount = 1; // Padrão de quantidade para items.

                $itemBoleto = new Item($name, $value, $amount);
                $payment->addItem($itemBoleto);
            }
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
        }
    }
}
