<?php

namespace PaymentGateway\Methods\Pix;

use PaymentGateway\Logging\TransactionLogger;
use DateInterval;
use DateTime;
use PaymentGateway\Methods\Pix\Discount;
use PaymentGateway\Methods\Pix\Pix;
use PaymentGateway\Methods\Pix\PixFormatter;

class PixConfig
{
    private Pix $payment;
    private PixFormatter $formatter;

    public function __construct($orderAttributes)
    {
        $this->payment = new Pix();
        $this->formatter = new PixFormatter();
        $this->setExpiracao($orderAttributes['pixDays']);
        $this->setChave($orderAttributes['pixKey']);
    }

    // Setter para Discount
    public function setDiscount(?Discount $discount): self
    {
        $this->payment->setDiscount($discount);

        return $this;
    }

    // Setter para Valor
    public function setValor(?float $valor): self
    {

        $this->payment->setValor($valor); // Define o valor formatado no objeto


        return $this;
    }

    // Setter para Expiracao
    public function setExpiracao(?int $dias): self
    {
        $expiracao = 3600 * 24 * $dias;
        $this->payment->setExpiracao($expiracao);
        return $this;
    }

    // Setter para Chave
    public function setChave(?string $chave): self
    {
        $this->payment->setChave($chave);
        return $this;
    }

    /**
     * Retorna a configuração do Pix  formatada como um array.
     *
     * @return array
     */
    public function getConfig(): array
    {
        try {
            $config = $this->formatter->format($this->payment);
            TransactionLogger::log(json_encode($config), TransactionLogger::DEBUG_LOG);

            return $config;
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
            return [];
        }
    }
}
