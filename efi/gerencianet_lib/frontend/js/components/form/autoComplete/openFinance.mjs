import { applyValuesToInputs } from './applyValuesToInputs.mjs';
import { getCustomerValues } from './getCustomerValues.mjs';
export async function addOF() {
    const cliente = await getCustomerValues();
    const inputsToAddAutoComplete = [
        cliente.documento.length == 11 ? {
            inputName: "#documentClientOF",
            inputValue: cliente.documento
        } : {
            inputName: "#documentPJClientOF",
            inputValue: cliente.documento
        }
    ];
    applyValuesToInputs(inputsToAddAutoComplete);

}