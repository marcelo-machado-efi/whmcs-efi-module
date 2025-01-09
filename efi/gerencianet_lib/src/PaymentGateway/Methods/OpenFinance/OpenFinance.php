<?php

namespace PaymentGateway\Methods\OpenFinance;

use PaymentGateway\Logging\TransactionLogger;
use PaymentGateway\Methods\OpenFinance\Favorecido;
use PaymentGateway\Methods\OpenFinance\Pagador;

class OpenFinance
{
    private ?Favorecido $favorecido = null;
    private ?Pagador $pagador = null;
    private ?float $valor = null;
    private ?string $idProprio = null;

    /**
     * Obtém o favorecido.
     *
     * @return Favorecido|null
     */
    public function getFavorecido(): ?Favorecido
    {
        return $this->favorecido;
    }

    /**
     * Define o favorecido.
     *
     * @param Favorecido|null $favorecido
     */
    public function setFavorecido(?Favorecido $favorecido): void
    {
        $this->favorecido = $favorecido;
    }

    /**
     * Obtém o pagador.
     *
     * @return Pagador|null
     */
    public function getPagador(): ?Pagador
    {
        return $this->pagador;
    }

    /**
     * Define o pagador.
     *
     * @param Pagador|null $pagador
     */
    public function setPagador(?Pagador $pagador): void
    {
        $this->pagador = $pagador;
    }

    /**
     * Obtém o valor.
     *
     * @return float|null
     */
    public function getValor(): ?float
    {
        return $this->valor;
    }

    /**
     * Define o valor.
     *
     * @param float|null $valor
     */
    public function setValor(?float $valor): void
    {
        $this->valor = $valor;
    }

    /**
     * Obtém o ID próprio.
     *
     * @return string|null
     */
    public function getIdProprio(): ?string
    {
        return $this->idProprio;
    }

    /**
     * Define o ID próprio.
     *
     * @param string|null $idProprio
     */
    public function setIdProprio(?string $idProprio): void
    {
        $this->idProprio = $idProprio;
    }
}
