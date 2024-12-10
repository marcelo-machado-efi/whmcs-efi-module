<?php

require_once __DIR__ . '/../../../../init.php';

include_once __DIR__ . '../../efi/gerencianet_lib/Gerencianet_WHMCS_Interface.php';
include_once __DIR__ . '../../efi/gerencianet_lib/database_interaction.php';

use WHMCS\Config\Setting;

App::load_function('gateway');
App::load_function('invoice');

// Função para registrar erros no log de transações do WHMCS
function logError($gatewayParams, $errorMessage, $errorData = [])
{
    logTransaction($gatewayParams['name'], $errorData, "Erro: $errorMessage");
}

// Função para validar o webhook Pix com HMAC e registrar erros
function validateWebhookPix($gatewayParams, $hmac): bool
{
    try {
        $systemUrl = Setting::getValue('SystemURL');
        if (!$systemUrl) {
            logError($gatewayParams, 'URL do sistema não encontrada.');
            return false;
        }

        $url = $systemUrl . '/modules/gateways/callback/efi/pix.php';

        if (!isset($gatewayParams['clientIdProd'])) {
            logError($gatewayParams, 'Client ID não encontrado nos parâmetros do gateway.');
            return false;
        }

        $hmacStorage = hash_hmac('sha256', $url, $gatewayParams['clientIdProd']);

        if ($hmacStorage !== $hmac) {
            logError($gatewayParams, 'Falha na validação do HMAC.', ['HMAC Recebido' => $hmac]);
            return false;
        }

        return true;
    } catch (Exception $e) {
        logError($gatewayParams, 'Erro ao validar webhook.', ['Mensagem' => $e->getMessage()]);
        return false;
    }
}

// Fetch gateway configuration parameters
$gatewayModuleName = 'efi';
$gatewayParams = getGatewayVariables($gatewayModuleName);
$mtls = ($gatewayParams['mtls'] != 'on');

// Tratamento de erros na lógica principal
if ($mtls) {
    try {
        $hmac = $_GET['hmac'] ?? null;

        if (validateWebhookPix($gatewayParams, $hmac)) {
            // Hook data retrieving
            @ob_clean();

            $postData = json_decode(file_get_contents('php://input'));

            // Aqui a lógica original é mantida, apenas logando os erros
            if (isset($postData->evento) && isset($postData->data_criacao)) {
                header('HTTP/1.0 200 OK');
                exit();
            }

            $pixPaymentData = $postData->pix;

            // Hook manipulation
            if (empty($pixPaymentData)) {
                logError($gatewayParams, 'Pagamento Pix não recebido pelo Webhook.');
                header('HTTP/1.1 400 Bad Request');
                exit('Pagamento Pix não recebido.');
            } else {
                header('HTTP/1.0 200 OK');
                $tableName = 'tblgerencianetpix';
                $txID = $pixPaymentData[0]->txid;
                $e2eID = $pixPaymentData[0]->endToEndId;

                $success = !empty($e2eID);

                // Retrieving Invoice ID from 'tblgerencianetpix'
                $savedInvoice = find($tableName, 'txid', $txID);

                // Checking if the invoice has already been paid
                if (empty($savedInvoice['e2eid'])) {
                    // Validate Callback Invoice ID
                    $invoiceID = checkCbInvoiceID($savedInvoice['invoiceid'], $gatewayParams['name']);

                    $conditions = [
                        'invoiceid' => $invoiceID,
                    ];

                    $dataToUpdate = [
                        'e2eid' => $e2eID,
                    ];

                    // Saving e2eid in table 'tblgerencianetpix'
                    update($tableName, $conditions, $dataToUpdate);

                    $pixDiscount = str_replace('%', '', $gatewayParams['pixDiscount']);

                    if ($success) {
                        $paymentFee = '0.00';
                        $paymentAmount = $pixPaymentData[0]->valor;
                        if ($pixDiscount > 0) {
                            extra_amounts_Gerencianet_WHMCS($savedInvoice['invoiceid'], $pixDiscount, 1);
                        }

                        addInvoicePayment($invoiceID, $txID, $paymentAmount, $paymentFee, $gatewayModuleName);
                    } else {
                        logError($gatewayParams, "Pagamento Pix não efetuado para a Fatura #$invoiceID");
                    }
                }
            }
        }
    } catch (Exception $e) {
        logError($gatewayParams, 'Erro no processamento do Webhook.', ['Mensagem' => $e->getMessage()]);
    }
}
