<?php

namespace PaymentGateway\Methods\OpenFinance;

class ContaBanco
{
    private string $nome;
    private string $documento;
    private string $codigoBanco;
    private string $agencia;
    private string $conta;
    private string $tipoConta;

    /**
     * Construtor da classe ContaBanco.
     *
     * @param array $orderData
     */
    public function __construct(
        array $orderData
    ) {
        $this->nome = $orderData["nome"];
        $this->documento = $orderData["documento"];
        $this->codigoBanco = "09089356";
        $this->agencia = $orderData["agencia"];
        $this->conta = $orderData["conta"];
        $this->tipoConta = $orderData["tipoConta"];
    }

    /**
     * Obtém o nome do titular da conta.
     *
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * Define o nome do titular da conta.
     *
     * @param string $nome
     */
    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    /**
     * Obtém o documento do titular (CPF ou CNPJ).
     *
     * @return string
     */
    public function getDocumento(): string
    {
        return $this->documento;
    }

    /**
     * Define o documento do titular.
     *
     * @param string $documento
     */
    public function setDocumento(string $documento): void
    {
        $this->documento = $documento;
    }

    /**
     * Obtém o código do banco.
     *
     * @return string
     */
    public function getCodigoBanco(): string
    {
        return $this->codigoBanco;
    }

    /**
     * Define o código do banco.
     *
     * @param string $codigoBanco
     */
    public function setCodigoBanco(string $codigoBanco): void
    {
        $this->codigoBanco = $codigoBanco;
    }

    /**
     * Obtém o número da agência.
     *
     * @return string
     */
    public function getAgencia(): string
    {
        return $this->agencia;
    }

    /**
     * Define o número da agência.
     *
     * @param string $agencia
     */
    public function setAgencia(string $agencia): void
    {
        $this->agencia = $agencia;
    }

    /**
     * Obtém o número da conta.
     *
     * @return string
     */
    public function getConta(): string
    {
        return $this->conta;
    }

    /**
     * Define o número da conta.
     *
     * @param string $conta
     */
    public function setConta(string $conta): void
    {
        $this->conta = $conta;
    }

    /**
     * Obtém o tipo de conta.
     *
     * @return string
     */
    public function getTipoConta(): string
    {
        return $this->tipoConta;
    }

    /**
     * Define o tipo de conta.
     *
     * @param string $tipoConta
     */
    public function setTipoConta(string $tipoConta): void
    {
        $this->tipoConta = $tipoConta;
    }
}
