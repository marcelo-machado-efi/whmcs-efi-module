import { addBanksOptionsOpenFinance } from "../../openFinance/addBanksOptionsOpenFinance.mjs";
export const handlerShowMethods = () => {
    const boletoAtivo = !($('.boletoOption').val());
    const cartaoAtivo = !($('.creditOption').val());
    const pixAtivo = !($('.pixOption').val());

    const openFinanceAtivo = ($('.openFinanceOption').val());
    console.log($('.creditOption').val());


    boletoAtivo && removeOption('billet')
    cartaoAtivo && removeOption('creditCard')
    pixAtivo && removeOption('pix')

    if (openFinanceAtivo) {
        addBanksOptionsOpenFinance();

    } else {
        removeOption('openFinance')
    }
    addFocusInFirstOptionActive()



}


function removeOption(optionRemove) {
    const option = $(`a[href="#${optionRemove}"]`);
    const tabOptionPayment = $(`#${optionRemove}`);
    $(option).remove();
    $(tabOptionPayment).remove();

}

function addFocusInFirstOptionActive() {
    const firstOption = $('a[data-toggle="tab"]').first(); // Seleciona o primeiro elemento <a> com data-toggle="tab"

    // Obtém o valor do atributo href do primeiro elemento
    const nameFirstTabIsActive = firstOption.attr('href');

    // Seleciona o elemento que corresponde ao valor do href
    const firstTabActive = $(nameFirstTabIsActive);
    $('#descriptionPayment').html($(firstOption).text().trim());
    // Adiciona classes ao primeiro elemento <a>
    firstOption.addClass("active1 active").removeClass("bg-light");

    // Adiciona classes ao elemento correspondente ao href
    firstTabActive.addClass("in active");
}