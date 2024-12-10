import { applyValuesToInputs } from './applyValuesToInputs.mjs';
import { getCustomerValues } from './getCustomerValues.mjs';
export async function addPix() {
    const cliente = await getCustomerValues();
    const inputsToAddAutoComplete = [{
        inputName: "#clientNamePix",
        inputValue: cliente.fullName
    }, {
        inputName: "#documentClientPix",
        inputValue: cliente.documento
    }];
    applyValuesToInputs(inputsToAddAutoComplete);

}