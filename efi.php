<?php

require 'efi/gerencianet-sdk/autoload.php';
require_once 'efi/vendor/autoload.php';


include_once 'efi/gerencianet_lib/api_interaction.php';

include_once 'efi/gerencianet_lib/database_interaction.php';

include_once 'efi/gerencianet_lib/handler/exception_handler.php';

include_once 'efi/gerencianet_lib/GerencianetValidation.php';

include_once 'efi/gerencianet_lib/GerencianetIntegration.php';

include_once 'efi/gerencianet_lib/functions/gateway/Billet.php';

include_once 'efi/gerencianet_lib/functions/gateway/CreditCard.php';

include_once 'efi/gerencianet_lib/functions/gateway/PIX.php';

include_once 'efi/gerencianet_lib/functions/gateway/OpenFinance.php';

include_once 'efi/gerencianet_lib/Gerencianet_WHMCS_Interface.php';

include_once 'efi/gerencianet_lib/functions/viewAutoComplete/viewAutoComplete.php';

include_once 'efi/gerencianet_lib/functions/admin/ValidationAdminFileds.php';

include_once 'efi/gerencianet_lib/models/invoice/InvoiceProcessor.php';

include_once 'efi/gerencianet_lib/models/subscription/database/SubscriptionEfiDataBase.php';

include_once 'efi/gerencianet_lib/models/subscription/handler/SubscriptionEfiHandler.php';

use PaymentGateway\Methods\PaymentMethodFactory;
use PaymentGateway\Database\EfiDatabases;
use PaymentGateway\Database\BoletoDAO;
use PaymentGateway\Database\PixDAO;
use PaymentGateway\DTOs\Config\ConfigAPIDTO;
use PaymentGateway\Methods\Pix\PixQrCodeGenerator;

use Efi\EfiPay;


use PaymentGateway\Logging\TransactionLogger;

if (!defined('WHMCS')) {

    die('This file cannot be accessed directly');
}



/**

 * Define gateway configuration options.

 *

 * The fields you define here determine the configuration options that are

 * presented to administrator users when activating and configuring your

 * payment gateway module for use.

 *

 * Supported field types include:

 * * text

 * * password

 * * yesno

 * * dropdown

 * * radio

 * * textarea

 *

 * Examples of each field type and their possible configuration parameters are

 * provided in the sample function below.

 *

 * @see https://developers.whmcs.com/payment-gateways/configuration/

 *

 * @return array

 */

function efi_config()

{

    return array(

        'FriendlyName' => array(

            'Type' => 'System',

            'Value' => 'Efí'

        ),

        'gn_style' => array(

            'Description' => '

            <style type="text/css">

                .icon-required{

                    font-size:0.6em;

                    vertical-align: middle;

                    color: red;

                    margin-right: 4px;

                    

                }

                .icon-weight{

                    font-weight: normal;

                }

                .card{
                    padding: 25px;
                }

                .text-color-efi{
                    color: #f26522;
                }
                .bg-efi{
                    background-color: #f26522;
                    color: white;
                    margin-top:10px;
                }
                .bg-efi:hover{
                    background-color: #d9551d;
                    color: white;
                }

            </style>

            '

        ),

        'clientIdProd' => array(

            'FriendlyName' => '<i   data-toggle="tooltip" data-placement="top" title="Obrigatório" class="test_toggle fas icon-required icon-weight fa-xs fa-asterisk"></i>Client_Id de Produção ',

            'Type' => 'text',

            'Size' => '250',

            'Default' => '',

            'Description' => '',

        ),

        'clientSecretProd' => array(

            'FriendlyName' => '<i  data-toggle="tooltip" data-placement="top" title="Obrigatório" class="fas icon-required icon-weight fa-xs fa-asterisk"></i>Client_Secret de Produção ',

            'Type' => 'text',

            'Size' => '250',

            'Default' => '',

            'Description' => '',

        ),

        'clientIdSandbox' => array(

            'FriendlyName' => '<i  data-toggle="tooltip" data-placement="top" title="Obrigatório" class="fas icon-required icon-weight fa-xs fa-asterisk"></i>Client_Id de Sandbox  ',

            'Type' => 'text',

            'Size' => '250',

            'Default' => '',

            'Description' => '',

        ),

        'clientSecretSandbox' => array(

            'FriendlyName' => '<i  data-toggle="tooltip" data-placement="top" title="Obrigatório" class="fas icon-required icon-weight fa-xs fa-asterisk"></i> Client_Secret de Sandbox ',

            'Type' => 'text',

            'Size' => '250',

            'Default' => '',

            'Description' => '',

        ),

        'idConta'           => array(

            'FriendlyName'  => '<i  data-toggle="tooltip" data-placement="top" title="Obrigatório" class="fas icon-required icon-weight fa-xs fa-asterisk"></i> Identificador da Conta ',

            'Type'          => 'text',

            'Size'          => '32',

            'Description'   => '',

        ),



        'whmcsAdmin'    => array(

            'FriendlyName'  => '<i  data-toggle="tooltip" data-placement="top" title="Obrigatório" class="fas icon-required icon-weight fa-xs fa-asterisk"></i> Usuário administrador do WHMCS ',

            'Type'          => 'text',

            'Description'   => 'Insira o nome do usuário administrador do WHMCS.',

            'Description'   => '',

        ),
        'activeBoleto'       => array(
            'FriendlyName'  => 'Boleto',
            'Type'          => 'yesno',
            'Description'   => '<span id="billet">Ativar boleto como forma de pagamento</span>',
        ),
        'tarifaBoleto'    => array(
            'FriendlyName'  => 'Tarifa do Boleto <i class="toggle_billet fas fa-question-circle" id="discount_billet" data-toggle="tooltip" data-placement="top" title="Coloque a tarifa aplicada no seu boleto no formato 00.00"></i>',
            'Type'          => 'text',
            'Size' => '3',
            'Default' => '00.00',
            'Description'   => '',
        ),
        'tipoDesconto'      => array(
            'FriendlyName'  => 'Tipo de desconto <i class="toggle_billet fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Escolha a forma do desconto: Porcentagem ou em Reais"></i>',
            'Type'          => 'dropdown',
            'Options'       => array(
                '1'         => '% (Porcentagem)',
                '2'         => 'R$ (Reais)',
            ),
            'Description'   => '',
        ),
        'descontoBoleto'    => array(
            'FriendlyName'  => 'Desconto do Boleto <i class="toggle_billet fas fa-question-circle" id="discount_billet" data-toggle="tooltip" data-placement="top" title="Desconto para pagamentos no boleto bancário."></i>',
            'Type'          => 'text',
            'Description'   => '',
        ),
        'numDiasParaVencimento' => array(
            'FriendlyName'      => 'Número de dias para o vencimento do boleto <i class="toggle_billet fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Número de dias corridos para o vencimento do boleto  depois  de sua criação"></i>',
            'Type'              => 'text',
            'Description'       => '',
        ),
        'sendEmailGN'       => array(
            'FriendlyName'  => '<span class="toggle_billet"></span>Email de cobraça - Efí',
            'Type'          => 'yesno',
            'Description'   => 'Marque esta opção se você deseja que a Efí envie emails de transações para o cliente final',
        ),
        'fineValue'         => array(
            'FriendlyName'  => 'Configuração de Multa <i class="toggle_billet fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Valor da multa se pago após o vencimento - informe em porcentagem (mínimo 0,01% e máximo 10%)."></i>',
            'Type'          => 'text',
            'Description'   => '',
        ),
        'interestValue'         => array(
            'FriendlyName'  => 'Configuração de Juros <i class="toggle_billet fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Valor de juros por dia se pago após o vencimento - informe em porcentagem (mínimo 0,001% e máximo 0,33%)."></i>',
            'Type'          => 'text',
            'Description'   => '',
        ),
        'message'      => array(
            'FriendlyName'  => 'Observação <i class="toggle_billet fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Permite incluir no boleto uma mensagem para o cliente (máximo de 80 caracteres)."></i>',
            'Type'          => 'text',
            'Size'          => '80',
            'Description'   => '',
        ),
        'activePix'       => array(
            'FriendlyName'  => 'Pix',
            'Type'          => 'yesno',
            'Description'   => '<span id="pix"> Ativar PIX como forma de pagamento</span>',
        ),
        'pixKey' => array(
            'FriendlyName' => 'Chave Pix <i class="toggle_pix fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Insira sua chave Pix padrão para recebimentos"></i>',
            'Type' => 'text',
            'Size' => '250',
            'Default' => '',
            'Description' => '',
        ),
        'pixDiscount' => array(
            'FriendlyName' => 'Desconto do Pix (%) <i class="toggle_pix fas fa-question-circle" id="discount_pix" data-toggle="tooltip" data-placement="top" title="Preencha um valor caso queira dar um desconto para pagamentos via Pix"></i>',
            'Type' => 'text',
            'Size' => '3',
            'Default' => '0%',
            'Description' => '',
        ),
        'pixDays' => array(
            'FriendlyName' => 'Validade da Cobrança Pix <i class="toggle_pix fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Tempo em dias de validade da cobrança"></i>',
            'Type' => 'text',
            'Size' => '3',
            'Default' => '1',
            'Description' => '',
        ),




        'activeOpenFinance'       => array(

            'FriendlyName'  => 'Open Finance',

            'Type'          => 'yesno',

            'Description'   => '<span id="open_finance">Ativar Open Finance como forma de pagamento</span>',

        ),

        'nome' => array(

            'FriendlyName' => 'Nome <i class="toggle_open_finance fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Nome do titular da  da conta Efí"></i>',

            'Type' => 'text',

            'Size' => '350',

            'Default' => '',

            'Description' => '',

        ),

        'documento' => array(

            'FriendlyName' => 'Documento <i class="toggle_open_finance fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Documento do titular da  da conta Efí"></i>',

            'Type' => 'text',

            'Size' => '350',

            'Default' => '',

            'Description' => '',

        ),

        'agencia' => array(

            'FriendlyName' => 'Agência <i class="toggle_open_finance fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Agência do titular da conta Efí"></i>',

            'Type' => 'text',

            'Size' => '2',

            'Default' => '0001',

            'Description' => '',

        ),

        'conta' => array(

            'FriendlyName' => 'Conta <i class="toggle_open_finance fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Conta do titular"></i>',

            'Type' => 'text',

            'Size' => '2',

            'Default' => '',

            'Description' => '',

        ),

        'tipoConta'      => array(

            'FriendlyName'  => 'Tipo de conta <i class="toggle_open_finance fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Tipo de conta do titular"></i>',

            'Type'          => 'dropdown',

            'Options'       => array(

                'CACC'         => 'Conta Corrente',

                'SLRY'         => 'Conta Salário',

                'SVGS'         => 'Conta Poupança',

                'TRAN'         => 'Conta de Transações',

            ),

            'Description'   => '',

        ),


        'activeCredit'       => array(

            'FriendlyName'  => 'Cartão de Credito',

            'Type'          => 'yesno',

            'Description'   => 'Ativar cartão de crédito como forma de pagamento',

        ),

        'sandbox' => array(

            'FriendlyName' => 'Sandbox',

            'Type' => 'yesno',

            'Description' => 'Habilita o modo Sandbox da Efí',

        ),

        'debug' => array(

            'FriendlyName' => 'Debug',

            'Type' => 'yesno',

            'Description' => 'Habilita o modo Debug',

        ),

        'mtls' => array(

            'FriendlyName' => 'Validar mTLS <i class=" fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Marque essa opção caso o seu servidor tenha sido configurado para realizar a validação mTLS"></i>',

            'Type' => 'yesno',

            'Default' => false,

            'Description' => 'Entenda os riscos de não configurar o mTLS acessando o link https://gnetbr.com/rke4baDVyd',

        ),

        'pixCert' => array(

            'FriendlyName' => 'Certificado de Autenticação <i class=" fas fa-question-circle " data-toggle="tooltip" data-placement="top" title="Insira o caminho do seu certificado .pem ou .p12 (Necessário somente no caso de utilização do Pix/Open Finance)"></i>',

            'Type' => 'text',

            'Size' => '350',

            'Default' => '/var/certs/cert.pem',

            'Description' => '',

        ),
        'gn_version' => array(
            'FriendlyName' => '',
            'Description' => '<div class="card text-center">
            <div class="card-header mb-4 ">
              <h1 class="text-color-efi">Plugin Efí v2.4</h1>
            </div>
            <div class="card-body">
              <p class="card-text">Esta versão inclui atualizações importantes para o funcionamento com a versão 8.9 do WHMCS.</p>
              <p class="card-text">Esta versão adiciona a recorrência ao plugin da Efí.</p>
              <a target="_blank" href="https://comunidade.sejaefi.com.br/?_gl=1*1uap0hm*_gcl_au*MjQxMzI2Njc5LjE3MjQxNjM1OTQ." class="btn bg-efi">Precisa de ajuda? Fale Conosco através do Discord</a>
            </div>
          </div>
          '
        ),

        'gn_script' => array(

            'Description' => '

            <script>
                function limitarDuasCasasDecimais(ids) {
                    
                    ids.forEach(function(id) {
                        let row = $("#discount_" + id).parents()[1];
                        let tdDiscount = $(row).children()[1];
                        let inputDiscount = $(tdDiscount).children()[0];
                        
                        $(inputDiscount).on("input", function() {
                            let value = $(this).val().replace(/[^0-9.]/g, "");
                            let hasDecimal = value.indexOf(".") >= 0;

                            if (hasDecimal) {
                                let decimalIndex = value.indexOf(".");
                                let decimalSubstring = value.substr(decimalIndex + 1);
                                if (decimalSubstring.length > 2) {
                                value = value.substr(0, decimalIndex + 3);
                                }
                            }

                            $(this).val(value);
                        });
                    
                    })
                }

                function isCheckedOptPayment(method){

                    let labelOptPaymentMethod = $("#" + method).parent();

                    let inputOptPaymentMethod = $(labelOptPaymentMethod).children()[1];

                    let isChecked      = $(inputOptPaymentMethod).is(":checked");

                    if(!isChecked){

                        toggleFieldsPaymentMethod(".toggle_" + method,0)

                    }

                    $(inputOptPaymentMethod).click(()=>{

                        toggleFieldsPaymentMethod(".toggle_" + method,250)

                    })

                }

                

                function toggleFieldsPaymentMethod(method, timeToggle){

                    let fields = $(method);

                    fields.each((i, field)=>{

                        let rowField = $(field).parents()[1];

                        $(rowField).fadeToggle(timeToggle);

                    })

                }

                isCheckedOptPayment("billet")

                isCheckedOptPayment("pix")
                isCheckedOptPayment("open_finance")
                limitarDuasCasasDecimais(["billet","pix"])

                

               

            </script>

            '

        )

    );
}



function efi_config_validate($params)

{
    // Creating table 'tblgerencianetpix'
    createGerencianetPixTable();
    OpenFinanceEfi::createEfiOFTable();
    EfiSubscriptionDatabase::createEfiSubscriptionTable();
    EfiDatabases::create();

    ValidationFieldsAdmin($params);
}



/**

 * Payment link.

 *

 * Required by third party payment gateway modules only.

 *

 * Defines the HTML output displayed on an invoice. Typically consists of an

 * HTML form that will take the user to the payment gateway endpoint.

 *

 * @param array $gatewayParams Payment Gateway Module Parameters

 *

 * @see https://developers.whmcs.com/payment-gateways/third-party-gateway/

 *

 * @return string

 */

function efi_link($gatewayParams)

{
    EfiSubscriptionDatabase::createEfiSubscriptionTable();

    /* **************************************** Verifica se a versão do PHP é compatível com o módulo ******************************** */



    if (version_compare(PHP_VERSION, '8.1') < 0) {

        $errorMsg = 'A versão do PHP do servidor onde o WHMCS está hospedado não é compatível com o módulo Efí.';

        if ($gatewayParams['debug'] == "on")

            logTransaction('efi', $errorMsg, 'Erro de Versão');



        return send_errors(array('Erro Inesperado: Ocorreu um erro inesperado. Entre em contato com o responsável do site.'));
    }





    $baseUrl = $gatewayParams['systemurl'];







    $identificadorDaConta = $gatewayParams['idConta'];



    $autoCompletetotal = generateAutoCompleteTotal($gatewayParams);
    $invoiceProcessor = new InvoiceProcessor($gatewayParams['invoiceid']);
    $allItemsIsRecurring = $invoiceProcessor->areAllItemsRecurring();
    $apiEnvironment = ($gatewayParams['sandbox'] == 'on') ? "sandbox" : "api";


    $paymentOptionsScript = "<div id='modal_content'></div>
    <script type=\"text/javascript\">
        var apiEnvironment = '$apiEnvironment';
        var identificadorDaConta = '$identificadorDaConta';
        var allItemsIsRecurring = '$allItemsIsRecurring';

        var inputEnviroment = $('<input />', {
            value: apiEnvironment,
            type: 'hidden',
            name: 'apiEnvironment',
            id: 'apiEnvironment'
        });
        $(document.body).append(inputEnviroment);

        var inputIdentificador = $('<input />', {
            value: identificadorDaConta,
            type: 'hidden',
            name: 'identificadorDaConta',
            id: 'identificadorDaConta'
        });
        $(document.body).append(inputIdentificador);

        var inputRecorrencia = $('<input />', {
            value: allItemsIsRecurring,
            type: 'hidden',
            name: 'allItemsIsRecurring',
            id: 'allItemsIsRecurring'
        });
        $(document.body).append(inputRecorrencia);
    </script>


    $autoCompletetotal
    <script defer type=\"text/javascript\" src=\"$baseUrl/modules/gateways/efi/gerencianet_lib/scripts/js/efi.min.js\"></script>

    ";














    if (!isset($_POST['paymentType']) || $_POST['paymentType'] == '') {

        return $paymentOptionsScript;
    } else {

        switch ($_POST['paymentType']) {

            case 'pix':
                return definedPixPayment($gatewayParams);
                break;

            case 'billet':
                return definedBilletPayment($gatewayParams);
                break;

            case 'creditCard':
                return definedCreditCardPayment($gatewayParams, $paymentOptionsScript);
                break;

            case 'openFinance':
                return definedOpenFinancePayment($gatewayParams);
                break;

            default:
                break;
        }
    }
}



/**

 * Refund transaction

 *

 * Called when a refund is requested for a previously successful transaction

 *

 * @param array $gatewayParams Payment Gateway Module Parameters

 *

 * @see https://developers.whmcs.com/payment-gateways/refunds/

 *

 * @return array Transaction response status

 */

function efi_refund($gatewayParams)

{

    //  Validating if required parameters are empty

    validateRequiredParams($gatewayParams);



    // Getting API Instance

    $api_instance = getGerencianetApiInstance($gatewayParams);



    // Refunding Pix Charge

    $responseData = refundCharge($api_instance, $gatewayParams);



    return array(

        'status' => $responseData['rtrId'] ? 'success' : 'error',

        'rawdata' => $responseData,

        'transid' => $responseData['rtrId'] ? $responseData['rtrId'] : 'Not Refunded',

    );
}


function definedOpenFinancePayment($gatewayParams)
{
    $gatewayParams['paramsOF'] = $_POST;
    $payment = PaymentMethodFactory::create("open_finance", $gatewayParams);

    $paymentResult = $payment->processPayment();
    $openFinanceEfi = new OpenFinanceEfi($gatewayParams);


    // Getting API Instance




    $openFinanceEfi->storePaymentIdentifier($paymentResult['identificadorPagamento'], $gatewayParams['invoiceid']);

    return "<script type=\"text/javascript\">window.open(\"" . $paymentResult["redirectURI"] . "\", \"_self\");</script>";
}



function definedPixPayment($gatewayParams)
{


    $gatewayParams['paramsPix'] = $_POST;


    $hasChargeToInvoice =  PixDAO::findByInvoiceId($gatewayParams["invoiceid"]);

    if ($hasChargeToInvoice) {
        $configApi = new ConfigAPIDTO($gatewayParams);
        $api_instance = new EfiPay($configApi->getApiConfig());
        $qrCode = new PixQrCodeGenerator($hasChargeToInvoice->locid, $api_instance);
        return $qrCode->getImgQrCode();
    }

    $isSubscription = !isset($gatewayParams['paramsPix']['subscriptionPix']);
    if ($isSubscription) {
        $invoiceProcessor = new InvoiceProcessor($gatewayParams['invoiceid']);
        $handlerSubscription = new SubscriptionEfiHandler();
        $items = $invoiceProcessor->processRecurringItems()['item_details'];
        foreach ($items  as  $item) {
            $handlerSubscription->createSubscription('pix', intval($item['relid']), $gatewayParams['paramsPix']);
        }
    }



    $payment = PaymentMethodFactory::create('pix', $gatewayParams);
    $qrCodeImg = $payment->processPayment();

    return $qrCodeImg;
}



function definedBilletPayment($gatewayParams)

{
    $invoiceId = $gatewayParams['invoiceid'];
    $hasChargeToInvoice = BoletoDAO::findByInvoiceId($invoiceId);
    if ($hasChargeToInvoice) {
        return  buttonGerencianet(null, $hasChargeToInvoice->link_pdf);
    }


    $gatewayParams['paramsBoleto'] = $_POST;

    $paymentMethodEfi = PaymentMethodFactory::create('boleto', $gatewayParams);


    $paymentSuccess = $paymentMethodEfi->processPayment();
    if ($paymentSuccess) {
        return  buttonGerencianet(null, $paymentSuccess);
    }


    $isSubscription = isset($gatewayParams['paramsBoleto']['subscriptionBillet']);
    if ($isSubscription) {
        $invoiceProcessor = new InvoiceProcessor($gatewayParams['invoiceid']);
        $handlerSubscription = new SubscriptionEfiHandler();
        $items = $invoiceProcessor->processRecurringItems()['item_details'];
        foreach ($items  as  $item) {
            $handlerSubscription->createSubscription('billet', intval($item['relid']), $gatewayParams['paramsBoleto']);
        }
    }
}



function definedCreditCardPayment($gatewayParams, $paymentOptionsScript)

{
    $gatewayParams['paramsCartao'] = $_POST;



    $paymentMethodEfi = PaymentMethodFactory::create('credit_card', $gatewayParams);


    $paymentSuccess = $paymentMethodEfi->processPayment();

    $isSubscription = isset($gatewayParams['paramsCartao']['subscriptionCard']);
    $pagamentoAprovado =  strpos($paymentSuccess, 'Pagamento Aprovado');
    if ($isSubscription && $pagamentoAprovado !== false) {
        $invoiceProcessor = new InvoiceProcessor($gatewayParams['invoiceid']);
        $handlerSubscription = new SubscriptionEfiHandler();
        $items = $invoiceProcessor->processRecurringItems()['item_details'];
        foreach ($items  as  $item) {
            $handlerSubscription->createSubscription('card', intval($item['relid']), $gatewayParams['paramsCartao']);
            $handlerSubscription->createSchedulePayment(intval($gatewayParams['invoiceid']), $gatewayParams['paramsCartao']['payment_token'], intval($item['relid']));
        }
    }
    if ($pagamentoAprovado) {
        return $paymentSuccess;
    }
    return ($paymentSuccess . $paymentOptionsScript);
}
