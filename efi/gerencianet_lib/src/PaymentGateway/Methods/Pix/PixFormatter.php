<?php

namespace PaymentGateway\Methods\Pix;

use PaymentGateway\Methods\Pix\Pix;

use PaymentGateway\Logging\TransactionLogger;

class PixFormatter
{
    /**
     * Formata o objeto Pix para um array.
     *
     * @param Pix $payment Objeto de pagamento a ser formatado.
     * @return array Estrutura formatada do Pix.
     */
    public function format(Pix $payment): array
    {
        try {
            $total = $payment->getDiscount()->getTotalWithDiscount($payment->getValor()); // Calcula o valor com desconto
            $total = number_format((float)$total, 2, '.', ''); // Formata para duas casas decimais com separador "."

            $pix = [];
            $pix["calendario"]["expiracao"] = $payment->getExpiracao();
            $pix["valor"]["original"] = (string)$total;
            $pix["chave"] = $payment->getChave();

            return $pix;
        } catch (\Throwable $th) {
            TransactionLogger::log($th->getMessage(), TransactionLogger::ERROR_LOG);
            return [];
        }
    }
}
