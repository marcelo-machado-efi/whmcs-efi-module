import { formataValorEmReal } from "../common/formatToCurrency.mjs";


export const addTotalValueCard = () => {
    const totalSemDesconto = $('.invoice_value').val();
    const spanValorTotal = $('#totalCard #spanValorTotal');
    const spanValorFinal = $('#totalCard #spanValorFinal');

    spanValorTotal.html(formataValorEmReal(totalSemDesconto));


    spanValorFinal.html(formataValorEmReal(totalSemDesconto));

}