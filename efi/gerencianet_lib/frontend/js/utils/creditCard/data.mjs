import EfiPay from "../../libs/efi-payment-token/payment-token-efi-esm.min.js";

export async function getCardData() {
    let cardData = {};
    const number = verifyLengthValue(16, $('#numCartao').val().replaceAll(' ', ''));
    const cvv = verifyLengthValue(3, $('#codSeguranca').val());
    const expiration = $('#vencimentoCartao').val().split('/');
    const expirationMonth = verifyLengthValue(2, expiration[0]);
    const expirationYear = verifyLengthValue(4, `20${expiration[1]}`);
    const brand = await getBrand(number);
    const reuse = true;

    cardData = { number, cvv, expirationMonth, expirationYear, brand, reuse };


    return cardData;

}

async function getBrand(cardNumber) {

    try {
        const brand = await EfiPay.CreditCard
            .setCardNumber(cardNumber)
            .verifyCardBrand();

        return brand;
    } catch (error) {

        return undefined;
    }

}

function verifyLengthValue(length, value) {

    return (value.length == length) ? value : '';

}