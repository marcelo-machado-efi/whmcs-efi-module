import EfiPay from "../../libs/efi-payment-token/payment-token-efi-esm.min.js";
import { getCardData } from "./data.mjs";
import { getAccountConfiguration } from "./getAccountConfiguration.mjs";

export async function getPaymentToken() {
    try {
        const cardData = await getCardData();
        const accountConfig = getAccountConfiguration();
        const result = await EfiPay.CreditCard
            .setAccount(accountConfig.identificadorDaConta)
            .setEnvironment(accountConfig.apiEnvironment) // 'production' or 'sandbox'
            .setCreditCardData(cardData)
            .getPaymentToken();

        const payment_token = result.payment_token;
        const card_mask = result.card_mask;


        return payment_token;
    } catch (error) {
        console.log("Código: ", error.code);
        console.log("Nome: ", error.error);
        console.log("Mensagem: ", error.error_description);

        return null;
    }



}