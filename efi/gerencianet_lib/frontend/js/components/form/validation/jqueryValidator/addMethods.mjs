import { validateDocument, validateTelephone, validateCEP } from '../common/validations.mjs';
import { watcherInstallments } from '../../../../utils/creditCard/installments.mjs';
// Adicionando método de validação para Documento
$.validator.addMethod("document", function(value, element) {
    return this.optional(element) || validateDocument(value);
}, "Por favor, insira um documento válido.");

// Adicionando método de validação para telefone
$.validator.addMethod("validateTelephone", function(value, element) {

    return this.optional(element) || validateTelephone(value);
}, "Por favor, insira um telefone válido.");

// Adicionando método de validação para CEP
$.validator.addMethod("cep", function(value, element) {
    return this.optional(element) || validateCEP(value);
}, "Por favor, insira um CEP válido.");



$.validator.addMethod("watcherInstallments", function(value, element) {
    watcherInstallments();
    return true;

}, "Dados do cartão inválido.");

// Mensagens personalizadas
$.extend($.validator.messages, {
    required: "Campo obrigatório" // Mensagem personalizada para o required
});