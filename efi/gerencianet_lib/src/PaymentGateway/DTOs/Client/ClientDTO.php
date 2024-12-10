<?php



namespace PaymentGateway\DTOs\Client;



class ClientDTO
{
    public string $nome, $documento, $email, $telefone;
    function __construct(array $data)
    {
        $this->nome = $data['nome'];
        $this->documento = $data['documento'];
        $this->email = $data['email'];
        $this->telefone = $data['telefone'];
    }

    public function isJuridicalPerson(): bool
    {
        return strlen($this->documento) > 11;
    }
}
