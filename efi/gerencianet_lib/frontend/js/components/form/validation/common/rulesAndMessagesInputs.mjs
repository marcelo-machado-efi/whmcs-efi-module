export const getRules = (inputs) => {
    let rules = {};

    $(inputs).each((i, input) => {
        let name = $(input).attr('name');
        let type = $(input).attr('type');
        let required = $(input).prop('required');
        let personalValidation = $(input).attr('data-validation');

        if (name !== undefined && type !== 'hidden') {

            rules[name] = {};
            if (required) {
                rules[name].required = required;
            }

            if (personalValidation !== undefined) {
                rules[name][personalValidation] = true;
            }
        }

    })



    return rules;
}

export const getMessages = (inputs) => {
    let messages = {};

    $(inputs).each((i, input) => {
        let name = $(input).attr('name');
        let type = $(input).attr('type');
        let personalValidation = $(input).attr('data-validation');
        let msgPersonalValidation = $(input).parent().find('label').text().replace('*', '').toLowerCase();

        if (name !== undefined && type !== 'hidden') {

            messages[name] = {};

            if (personalValidation !== undefined) {
                messages[name][personalValidation] = `Por favor, insira um ${msgPersonalValidation} válido`;
            }
        }

    })
    return messages;


}