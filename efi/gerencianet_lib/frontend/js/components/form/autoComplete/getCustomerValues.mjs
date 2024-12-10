import { validateDocument } from '../validation/common/validations.mjs';
export async function getCustomerValues() {

    let url = new URL(window.location.href);
    let id = url.searchParams.get("id");
    let dataCustomer = await $.get(`modules/gateways/efi/gerencianet_lib/functions/frontend/ajax/ClientDataAjaxHandler.php?idFatura=${id}`);
    const clientObj = getClientObj(dataCustomer);

    return clientObj;
}




function getClientObj(dataCustomer) {
    const dataClient = JSON.parse(dataCustomer);
    let cliente = {};
    cliente.telefone = dataClient.phoneNumber.split('.')[1];
    cliente.fullName = `${dataClient.firstName} ${dataClient.lastName}`;
    cliente.email = dataClient.email;
    cliente.rua = dataClient.address1.split(',')[0];
    cliente.numero = dataClient.address1.split(',')[1];
    cliente.cidade = dataClient.city;
    cliente.cep = dataClient.postcode.replace('-', '');
    cliente.estado = dataClient.state;
    cliente.documento = getDocumentCliente(dataClient);
    return cliente;
}
/**
 * Função que verifica se algum dos custom fields é um documento válido
 */
function getDocumentCliente(dataClient) {
    let documentCustomField = '';
    $.each(dataClient.customFields, function(indexInArray, customField) {

        if (validateDocument(customField.value)) {
            documentCustomField = customField.value;
            return false;
        }
    });

    return documentCustomField;


}