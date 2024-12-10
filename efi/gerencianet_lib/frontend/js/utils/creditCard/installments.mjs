import EfiPay from "../../libs/efi-payment-token/payment-token-efi-esm.min.js";
import { getCardData } from "./data.mjs";
import { getAccountConfiguration } from "./getAccountConfiguration.mjs";


export async function watcherInstallments() {
    try {
        const cardData = await getCardData();
        let brand = cardData.brand;
        const total = $('.invoice_value').val() * 100;
        const accountConfig = getAccountConfiguration();
        window.lastBrand = (cardData.brand == window.lastBrand) ? cardData.brand : window.lastBrand;

        if (brand !== undefined && brand !== 'unsupported' && (cardData.brand != window.lastBrand)) {
            $('#numParcelas option').remove();
            $('#numParcelas').append('<option value=""> Insira os dados do seu cartão... </option>');
            console.log(identificadorDaConta);
            const dataInstallments = await EfiPay.CreditCard
                .setAccount(accountConfig.identificadorDaConta)
                .setEnvironment(accountConfig.apiEnvironment) // 'production' or 'sandbox'
                .setBrand(brand)
                .setTotal(total)
                .getInstallments();
            $('#numParcelas option').remove();
            $('#numParcelas').append('<option value=""> Insira os dados do seu cartão... </option>');

            $.each((dataInstallments.installments), function(i, installment) {
                let numParcela = i + 1;
                let msgOption = installment.has_interest ? `${(installment.currency)} com juros` : `${(installment.currency)} sem juros`;

                $('#numParcelas').append(`<option value="${(numParcela)}">${(numParcela)} x  R$ ${(msgOption)} </option>`);
            });

        }
        if (brand == undefined) {
            $('#numParcelas option').remove();
            $('#numParcelas').append('<option value=""> Insira os dados do seu cartão... </option>');
        }
        window.lastBrand = cardData.brand;
    } catch (error) {
        console.log(error);
    }



}