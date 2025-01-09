<?php

namespace PaymentGateway\Methods\OpenFinance;

class Favorecido
{
    private ContaBanco $contaBanco;

    /**
     * Construtor da classe Favorecido.
     *
     * @param ContaBanco $contaBanco
     */
    public function __construct(ContaBanco $contaBanco)
    {
        $this->contaBanco = $contaBanco;
    }

    /**
     * Obtém os dados da conta bancária do favorecido.
     *
     * @return ContaBanco
     */
    public function getContaBanco(): ContaBanco
    {
        return $this->contaBanco;
    }

    /**
     * Define os dados da conta bancária do favorecido.
     *
     * @param ContaBanco $contaBanco
     */
    public function setContaBanco(ContaBanco $contaBanco): void
    {
        $this->contaBanco = $contaBanco;
    }
}
