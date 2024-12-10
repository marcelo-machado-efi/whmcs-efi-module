const $ = window.jQuery;
import '../../../libs/jquery/jquery.validate.min.js';
import './jqueryValidator/addMethods.mjs';
import { errorPlacement, highlight, success, unhighlight, submitHandler } from './common/functions.mjs';
import { getMessages, getRules } from './common/rulesAndMessagesInputs.mjs';

export const addValidationForms = () => {
    $.each($('form'), function(i, form) {
        let inputsForm = $(form).find('input');
        let rules = getRules(inputsForm);
        let messages = getMessages(inputsForm);


        $(form).validate({
            rules, // Regras de validação
            messages, // Mensagens personalizadas
            success, // Função executada em caso de sucesso
            errorPlacement, // Função para posicionar mensagens de erro
            highlight, // Função para destacar campos com erro
            unhighlight,
            submitHandler: $(form).find('input[name=payment_token]').length ? submitHandler : null, // Função para remover destaque de campos corrigidos
        });
    });

};