import { formataValorEmReal } from "../common/formatToCurrency.mjs";


export const addTotalValueBillet = () => {
    const totalSemDesconto = $('.invoice_value').val();
    const descontoBoleto = $('.totalDescontoBoleto').val();
    const totalComDesconto = totalSemDesconto - descontoBoleto;
    const spanValorTotal = $('#totalBillet #spanValorTotal');
    const spanDesconto = $('#totalBillet #spanDesconto');
    const spanValorFinal = $('#totalBillet #spanValorFinal');

    spanValorTotal.html(formataValorEmReal(totalSemDesconto));
    if (descontoBoleto > 0) {
        spanDesconto.html(`-${formataValorEmReal(descontoBoleto)}`);
        spanValorTotal.closest('.d-flex').addClass('border-bottom-0');
        spanDesconto.closest('.col-12').removeClass('d-none');

    }

    spanValorFinal.html(formataValorEmReal(totalComDesconto));

}