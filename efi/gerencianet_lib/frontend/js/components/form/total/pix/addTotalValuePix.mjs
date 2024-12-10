import { formataValorEmReal } from "../common/formatToCurrency.mjs";


export const addTotalValuePix = () => {
    const totalSemDesconto = $('.invoice_value').val();
    const descontoPix = $('.totalDescontoPix').val();
    const totalComDesconto = totalSemDesconto - descontoPix;
    const spanValorTotal = $('#totalPix #spanValorTotal');
    const spanDesconto = $('#totalPix #spanDesconto');
    const spanValorFinal = $('#totalPix #spanValorFinal');

    console.log(totalComDesconto);
    spanValorTotal.html(formataValorEmReal(totalSemDesconto));
    if (descontoPix > 0) {
        spanDesconto.html(`-${formataValorEmReal(descontoPix)}`);
        spanValorTotal.closest('.d-flex').addClass('border-bottom-0');
        spanDesconto.closest('.col-12').removeClass('d-none');

    }

    spanValorFinal.html(formataValorEmReal(totalComDesconto));

}