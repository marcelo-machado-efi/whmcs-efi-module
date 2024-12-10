export const getAccountConfiguration = () => {
    const identificadorDaConta = $('#identificadorDaConta').val();
    const apiEnvironment = $('#apiEnvironment').val();
    const accountConfig = { identificadorDaConta, apiEnvironment };

    return accountConfig;
}