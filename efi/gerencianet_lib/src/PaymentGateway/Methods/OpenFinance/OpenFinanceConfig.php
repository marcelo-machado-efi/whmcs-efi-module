<?php

namespace PaymentGateway\Methods\OpenFinance;

use PaymentGateway\Logging\TransactionLogger;
use PaymentGateway\Methods\OpenFinance\OpenFinance;
use PaymentGateway\Methods\OpenFinance\OpenFinanceFormatter;

class OpenFinanceConfig
{
    private OpenFinance $payment;
    private OpenFinanceFormatter $formatter;

    public function __construct()
    {
        $this->payment = new OpenFinance();
        $this->formatter = new OpenFinanceFormatter();
    }

    /**
     * Define o favorecido na configuração do pagamento.
     *
     * @param Favorecido $favorecido
     */
    public function setFavorecido(Favorecido $favorecido): self
    {
        $this->payment->setFavorecido($favorecido);

        return $this;
    }

    /**
     * Define o pagador na configuração do pagamento.
     *
     * @param Pagador $pagador
     */
    public function setPagador(Pagador $pagador): self
    {
        $this->payment->setPagador($pagador);
        return $this;
    }

    /**
     * Define o valor na configuração do pagamento.
     *
     * @param float $valor
     */
    public function setValor(float $valor): self
    {
        $this->payment->setValor($valor);
        return $this;
    }

    /**
     * Define o ID próprio na configuração do pagamento.
     *
     * @param string $idProprio
     */
    public function setIdProprio(string $idProprio): self
    {
        $this->payment->setIdProprio($idProprio);
        return $this;
    }

    /**
     * Retorna a configuração do Pix formatada como um array.
     *
     * @return array
     */
    public function getConfig(): array
    {
        try {
            $config = $this->formatter->format($this->payment);

            return $config;
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return [];
        }
    }
}
