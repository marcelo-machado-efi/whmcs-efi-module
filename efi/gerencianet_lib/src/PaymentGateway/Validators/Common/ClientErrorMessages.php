<?php

namespace PaymentGateway\Validators\Common;

class ClientErrorMessages
{
    public const NAME_ERROR_MESSAGE = "Nome Inválido: O nome é muito curto. Você deve digitar seu nome completo.";
    public const EMAIL_ERROR_MESSAGE = "Email Inválido: O email informado é inválido ou não existe.";
    public const BIRTHDATE_ERROR_MESSAGE = "Data de nascimento Inválida: A data de nascimento informada deve seguir o padrão Ano-mês-dia.";
    public const PHONENUMBER_ERROR_MESSAGE = "Telefone Inválido: O telefone informado não existe ou o DDD está incorreto.";
    public const DOCUMENT_NULL_ERROR_MESSAGE = "Documento Nulo: O campo referente à CPF e/ou CNPJ não existe ou não está preenchido.";
    public const CPF_ERROR_MESSAGE = "Documento Inválido: O número do CPF do cliente é inválido.";
    public const CNPJ_ERROR_MESSAGE = "Documento Inválido: O número do CNPJ do cliente é inválido.";
    public const CORPORATE_ERROR_MESSAGE = "Razão Social Inválida: O nome da empresa é inválido. Você deve digitar no campo \"Empresa\" de seu WHMCS o nome que consta na Receita Federal.";
    public const CORPORATE_NULL_ERROR_MESSAGE = "Razão Social Nula: O campo \"Empresa\" de seu WHMCS não está preenchido.";
    public const INTEGRATION_ERROR_MESSAGE = "Erro Inesperado: Ocorreu um erro inesperado. Entre em contato com o responsável do site.";
    public const CARD_ERROR_MESSAGE = "Cartão de crédito inválido: O número do cartão de crédito informado não é válido.";
}
