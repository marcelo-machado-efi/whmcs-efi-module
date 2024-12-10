<?php

namespace PaymentGateway\Validators\Boleto;

use PaymentGateway\Validators\Common\CommonValidations;
use PaymentGateway\Validators\Common\ClientErrorMessages;
use PaymentGateway\DTOs\Client\ClientDTO;


class BoletoValidation
{
    private ClientDTO $cliente;
    private array $mensagensErros = [];

    public function __construct(ClientDTO $client)
    {

        $this->cliente = $client;
    }

    public function validate(): array
    {
        $this->validateDocument();
        $this->validateName();
        $this->validatePhone();
        $this->validateEmail();

        return $this->mensagensErros;
    }

    private function validateDocument(): void
    {
        $documento = $this->cliente->documento;

        if ($this->cliente->isJuridicalPerson()) {
            if (!CommonValidations::_cnpj($documento)) {
                $this->addError(ClientErrorMessages::CNPJ_ERROR_MESSAGE);
            }
        } else {
            if (!CommonValidations::_cpf($documento)) {
                $this->addError(ClientErrorMessages::CPF_ERROR_MESSAGE);
            }
        }
    }

    private function validateName(): void
    {
        $nome = $this->cliente->nome;

        if ($this->cliente->isJuridicalPerson()) {
            if (!CommonValidations::_corporate($nome)) {
                $this->addError(ClientErrorMessages::CORPORATE_ERROR_MESSAGE);
            }
        } else {
            if (!CommonValidations::_name($nome)) {
                $this->addError(ClientErrorMessages::NAME_ERROR_MESSAGE);
            }
        }
    }

    private function validatePhone(): void
    {
        $telefone = $this->cliente->telefone;

        if ($telefone !== '' && !CommonValidations::_phone_number($telefone)) {
            $this->addError(ClientErrorMessages::PHONENUMBER_ERROR_MESSAGE);
        }
    }

    private function validateEmail(): void
    {
        $email = $this->cliente->email;

        if (!CommonValidations::_email($email)) {
            $this->addError(ClientErrorMessages::EMAIL_ERROR_MESSAGE);
        }
    }

    private function addError(string $message): void
    {
        $this->mensagensErros[] = $message;
    }
}
