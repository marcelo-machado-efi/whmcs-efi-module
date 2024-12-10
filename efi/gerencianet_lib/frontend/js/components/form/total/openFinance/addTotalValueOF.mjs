import { formataValorEmReal } from "../common/formatToCurrency.mjs";


export const addTotalValueOF = () => {
    const totalSemDesconto = $('.invoice_value').val();
    const spanValorTotal = $('#totalOF #spanValorTotal');
    const spanValorFinal = $('#totalOF #spanValorFinal');

    spanValorTotal.html(formataValorEmReal(totalSemDesconto));


    spanValorFinal.html(formataValorEmReal(totalSemDesconto));

}