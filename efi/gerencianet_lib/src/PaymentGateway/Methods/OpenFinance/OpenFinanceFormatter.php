<?php

namespace PaymentGateway\Methods\OpenFinance;

use PaymentGateway\Methods\OpenFinance\OpenFinance;

use PaymentGateway\Logging\TransactionLogger;

class OpenFinanceFormatter
{
    /**
     * Formata o objeto Pix para um array.
     *
     * @param OpenFinance $payment Objeto de pagamento a ser formatado.
     * @return array Estrutura formatada do Pix.
     */
    public function format(OpenFinance $payment): array
    {
        try {
            $valor = $payment->getValor();
            $valor = number_format((float)$valor, 2, '.', ''); // Formata para duas casas decimais com separador "."

            $openFinance = [];
            $openFinance["pagador"] = [
                "idParticipante" => $payment->getPagador()->getIdParticipante(),
                "cpf" => $payment->getPagador()->getCpf()
            ];

            if ($payment->getPagador()->getCnpj()) {
                $openFinance["pagador"]["cnpj"] = $payment->getPagador()->getCnpj();
            }

            $openFinance["favorecido"]["contaBanco"] = [
                "nome" => $payment->getFavorecido()->getContaBanco()->getNome(),
                "documento" => $payment->getFavorecido()->getContaBanco()->getDocumento(),
                "codigoBanco" => $payment->getFavorecido()->getContaBanco()->getCodigoBanco(),
                "agencia" => $payment->getFavorecido()->getContaBanco()->getAgencia(),
                "conta" => $payment->getFavorecido()->getContaBanco()->getConta(),
                "tipoConta" => $payment->getFavorecido()->getContaBanco()->getTipoConta(),
            ];
            $openFinance["valor"] = (string)$valor;
            $openFinance["idProprio"] = $payment->getIdProprio();

            return $openFinance;
        } catch (\Throwable $th) {
            TransactionLogger::log($th->getMessage(), TransactionLogger::ERROR_LOG);
            return [];
        }
    }
}
