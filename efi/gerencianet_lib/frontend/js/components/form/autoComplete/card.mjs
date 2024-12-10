import { applyValuesToInputs } from './applyValuesToInputs.mjs';
import { getCustomerValues } from './getCustomerValues.mjs';
export async function addCard() {
    const cliente = await getCustomerValues();
    const inputsToAddAutoComplete = [{
        inputName: "#nameCredit",
        inputValue: cliente.fullName
    }, {
        inputName: "#clientEmailCredit",
        inputValue: cliente.email
    }, {
        inputName: "#documentClientCredit",
        inputValue: cliente.documento
    }, {
        inputName: "#telephoneCredit",
        inputValue: cliente.telefone
    }, {
        inputName: "#rua",
        inputValue: cliente.rua
    }, {
        inputName: "#numero",
        inputValue: cliente.numero
    }, {
        inputName: "#cidade",
        inputValue: cliente.cidade
    }, {
        inputName: "#bairro",
        inputValue: ''
    }, {
        inputName: "#cep",
        inputValue: cliente.cep
    }, {
        inputName: "#estado",
        inputValue: cliente.estado
    }, {
        inputName: "#dataNasce",
        inputValue: ''
    }];
    applyValuesToInputs(inputsToAddAutoComplete);

}