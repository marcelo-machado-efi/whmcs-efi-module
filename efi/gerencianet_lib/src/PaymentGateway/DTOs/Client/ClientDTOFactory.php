<?php

namespace PaymentGateway\DTOs\Client;

use PaymentGateway\DTOs\Client\ClientDTO;

class ClientDTOFactory
{
    /**
     * Cria uma instância de ClientDTO a partir de um array de dados.
     *
     * @param array $data
     * @return ClientDTO
     * @throws \InvalidArgumentException se os dados estiverem incompletos ou inválidos.
     */
    public static function getClientDTO(array $data, string $paymentType): ClientDTO
    {
        switch ($paymentType) {
            case 'boleto':
                return self::getClientDTOBoleto($data);
                break;
            case 'cartao':
                return self::getClientDTOCartao($data);
                break;

            default:
                # code...
                break;
        }
    }

    private static function getClientDTOBoleto($data): ClientDTO
    {
        $documento = self::removeCaracteresNaoNumericos($data['paramsBoleto']['clientDocumentBillet']);
        $telefone = isset($data['paramsBoleto']['clientTelephoneBillet']) ? self::removeCaracteresNaoNumericos($data['paramsBoleto']['clientTelephoneBillet']) : null;
        $nome = $data['paramsBoleto']['clientName'];
        $email = $data['paramsBoleto']['clientEmail'];

        $data = [
            'nome' => $nome,
            'documento' => $documento,
            'email' => $email,
            'telefone' => $telefone
        ];
        return new ClientDTO($data);
    }
    private static function getClientDTOCartao($data): ClientDTO
    {
        $documento = self::removeCaracteresNaoNumericos($data['paramsCartao']['clientDocumentCredit']);
        $telefone = isset($data['paramsCartao']['clientTelephoneCredit']) ? self::removeCaracteresNaoNumericos($data['paramsCartao']['clientTelephoneCredit']) : null;
        $nome = $data['paramsCartao']['clientNameCredit'];
        $email = $data['paramsCartao']['clientEmailCredit'];

        $data = [
            'nome' => $nome,
            'documento' => $documento,
            'email' => $email,
            'telefone' => $telefone
        ];
        return new ClientDTO($data);
    }
    private static function removeCaracteresNaoNumericos($string)
    {

        return preg_replace('/[^0-9]/', '', $string);
    }
}
