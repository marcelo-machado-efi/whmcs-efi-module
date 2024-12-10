export const applyValuesToInputs = (inputs) => {
    inputs.forEach((inputObj) => {

        let input = $(`${inputObj.inputName}`);
        let div = $(`${inputObj.inputName}`).closest('.col-12');

        if (inputObj.inputValue != '') {

            div.fadeOut(400, function() {

                input.val(inputObj.inputValue);
                input.trigger('keydown');
                input.trigger('input');
                $(div).addClass('d-none');

            });
        } else {
            input.val(inputObj.inputValue);
            input.trigger('keydown');
            input.trigger('input');
            $(div).removeClass('d-none');
            div.fadeIn(400);
        }


    });
}