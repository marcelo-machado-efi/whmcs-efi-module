import { addTotalValueBillet } from "./billet/addTotalValueBillet.mjs"
import { addTotalValueCard } from "./creditCard/addTotalValueCard.mjs";
import { addTotalValueOF } from "./openFinance/addTotalValueOF.mjs";
import { addTotalValuePix } from "./pix/addTotalValuePix.mjs";


export const addTotalForPaymentMethods = () => {
    addTotalValueBillet();
    addTotalValuePix();
    addTotalValueCard();
    addTotalValueOF();
}