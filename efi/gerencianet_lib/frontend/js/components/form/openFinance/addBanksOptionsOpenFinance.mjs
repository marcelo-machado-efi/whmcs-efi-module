import { getBanksOptions } from "./getBanksOptions.mjs";

export async function addBanksOptionsOpenFinance() {
    const listBanks = await getBanksOptions();
    $('#bankOF').empty();
    $('#bankOF').append('<option value="">Escolha o banco...</option>');
    listBanks.participantes.forEach(function(value, i) {
        $('#bankOF').append(`<option value="${value.identificador}">${value.nome}</option>`)
    });

}