import { getPaymentToken } from '../../../../utils/creditCard/paymentToken.mjs';
export const errorPlacement = (error, element) => {
    $(error).css('color', 'red');

    var errorDiv = $("<div class='error-message d-flex'></div>");

    errorDiv.append(error);
    $(errorDiv).css({
        'padding-left': '5px'
    });

    $(element).parent().parent().append(errorDiv);
}

export const highlight = (element) => {
    $(element).parent().parent().find('.error-message').css('display', 'flex')
    $(element).parent().find('label').css('color', 'red');
    element.style.setProperty('margin-bottom', '0', 'important');
    $(element).removeClass('focus');

    $(element).addClass('form-control is-invalid');
}

export const unhighlight = (element) => {
    $(element).parent().find('label').css('color', '');
    $(element).parent().parent().find('.error-message').remove();
    element.style.setProperty('margin-bottom', '35px', 'important');
    $(element).removeClass('focus');
    $(element).removeClass('form-control is-invalid');



}

export const success = (label, element) => {

    $(element).css('margin-bottom', '');


}

export const submitHandler = async(form) => {
    const btnSubmit = $(form).find('input[type=submit]');

    $(btnSubmit).addClass('disabled');
    $(btnSubmit).attr('disabled', true);
    const paymentToken = await getPaymentToken();
    const inputPaymentToken = $(form).find('input[name=payment_token]');


    $(inputPaymentToken).val(paymentToken);

    if (paymentToken) {
        $('#numCartao').val('');
        $('#vencimentoCartao').val('');
        $('#codSeguranca').val('');
        form.submit();
    } else {
        $(btnSubmit).removeClass('disabled');
        $(btnSubmit).removeAttr('disabled');
        $(form).validate().showErrors({
            "numCartao": "Verifique os dados do cartão"
        });
    }


}