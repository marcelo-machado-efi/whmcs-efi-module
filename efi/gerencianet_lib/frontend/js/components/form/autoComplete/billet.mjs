import { applyValuesToInputs } from './applyValuesToInputs.mjs';
import { getCustomerValues } from './getCustomerValues.mjs';
export async function addBillet() {
    const cliente = await getCustomerValues();
    const inputsToAddAutoComplete = [{
        inputName: "#nameBillet",
        inputValue: cliente.fullName
    }, {
        inputName: "#clientEmailBillet",
        inputValue: cliente.email
    }, {
        inputName: "#documentClientBillet",
        inputValue: cliente.documento
    }, {
        inputName: "#telephoneBillet",
        inputValue: cliente.telefone
    }];
    applyValuesToInputs(inputsToAddAutoComplete);
}