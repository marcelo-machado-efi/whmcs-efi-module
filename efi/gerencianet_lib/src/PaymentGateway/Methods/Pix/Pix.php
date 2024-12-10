<?php

namespace PaymentGateway\Methods\Pix;

use PaymentGateway\Methods\Pix\Discount;
use PaymentGateway\Logging\TransactionLogger;

class Pix
{
    private ?Discount $discount = null;
    private ?float $valor = null;
    private ?int $expiracao = null;
    private ?string $chave = null;

    // Getter e Setter para Discount
    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function setDiscount(?Discount $discount): void
    {
        $this->discount = $discount;
    }

    // Getter e Setter para Valor
    public function getValor(): ?float
    {


        return $this->valor;
    }

    public function setValor(?float $valor): void
    {
        $this->valor = $valor;
    }

    // Getter e Setter para Expiracao
    public function getExpiracao(): ?int
    {
        return $this->expiracao;
    }

    public function setExpiracao(?int $expiracao): void
    {
        $this->expiracao = $expiracao;
    }

    // Getter e Setter para Chave
    public function getChave(): ?string
    {
        return $this->chave;
    }

    public function setChave(?string $chave): void
    {
        $this->chave = $chave;
    }
}
