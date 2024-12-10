export async function getBanksOptions() {
    let listBanks = await $.get(`modules/gateways/efi/gerencianet_lib/functions/frontend/ajax/OpenFinanceAjaxHandler.php?participants=1`);

    return JSON.parse(listBanks);
}