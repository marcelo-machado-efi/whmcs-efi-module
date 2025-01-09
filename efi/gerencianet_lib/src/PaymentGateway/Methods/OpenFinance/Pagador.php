<?php

namespace PaymentGateway\Methods\OpenFinance;

use InvalidArgumentException;
use PaymentGateway\Logging\TransactionLogger;

class Pagador
{
    private string $idParticipante;
    private string $cpf;
    private ?string $cnpj = null; // Opcional, pode ser null

    /**
     * Construtor da classe Pagador.
     *
     * @param array $orderData
     * @param string|null $cnpj
     */
    public function __construct(array $orderData)
    {
        $this->idParticipante = $orderData['paramsOF']["favoredbankOF"];
        $this->setCpf($orderData['paramsOF']["favoredDocumentOF"]);
        if (strlen($orderData['paramsOF']["favoredPJDocumentOF"]) > 0) {
            $this->cnpj = $orderData['paramsOF']["favoredPJDocumentOF"];
        }
    }

    /**
     * Obtém o ID do participante.
     *
     * @return string
     */
    public function getIdParticipante(): string
    {
        return $this->idParticipante;
    }

    /**
     * Obtém o CPF.
     *
     * @return string
     */
    public function getCpf(): string
    {
        $cpf = preg_replace('/\D/', '', $this->cpf);
        return $cpf;
    }

    /**
     * Define o CPF, validando o formato.
     *
     * @param string $cpf
     * @throws InvalidArgumentException
     */
    public function setCpf(string $cpf): void
    {
        if (!$this->isValidCpf($cpf)) {
            throw new InvalidArgumentException("CPF inválido: $cpf");
        }
        $this->cpf = $cpf;
    }

    /**
     * Obtém o CNPJ.
     *
     * @return string|null
     */
    public function getCnpj(): ?string
    {
        $cnpj = preg_replace('/\D/', '', $this->cnpj);

        return $cnpj;
    }

    /**
     * Define o CNPJ.
     *
     * @param string|null $cnpj
     */
    public function setCnpj(?string $cnpj): void
    {
        $this->cnpj = $cnpj;
    }

    /**
     * Valida o formato e os dígitos verificadores de um CPF.
     *
     * @param string $cpf
     * @return bool
     */
    private function isValidCpf(string $cpf): bool
    {
        // Remove máscara (pontos e traço)
        $cpf = preg_replace('/\D/', '', $cpf);

        // Verifica se o CPF possui exatamente 11 dígitos
        if (strlen($cpf) !== 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais (exemplo: 111.111.111-11)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Calcula e valida os dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}
