<?php


namespace PaymentGateway\Methods\Pix;

use Efi\EfiPay;


/**
 * Classe responsável por gerar o QrCode e devolver a imagem formatada
 */

class  PixQrCodeGenerator
{
    private int $locId;
    private EfiPay $apiInstance;
    function __construct(int $locId, EfiPay $apiInstance)
    {
        $this->locId = $locId;
        $this->apiInstance = $apiInstance;
    }

    /**
     * Retorna imagem formatada para pagamento do QrCode
     * @return string  Imagem formatada com o QrCode Pix
     */
    public function getImgQrCode(): string
    {
        $base64 = $this->getQrCodeBase64();
        $img = "<img id='imgPix' src='{$base64}' />\n";
        $copyButton = "<button class='btn btn-default' id='copyButton' onclick=\"copyQrCode('$base64}')\">Copiar QR Code</button>\n";
        $btnConfirmPayment = $this->getBtnConfirmPaymentPix();
        $scripts = $this->getScriptsForPaymentPix();

        $template = $img . $copyButton . $btnConfirmPayment . $scripts;

        return $template;
    }

    private function getQrCodeBase64(): string
    {
        $params = [
            "id" => $this->locId
        ];
        $responseApi = $this->apiInstance->pixGenerateQRCode($params);
        $base64 = $responseApi["imagemQrcode"];
        return $base64;
    }

    /**
     * Retorna o botão para verificar o pagamento Pix após o pagamento
     * @return string Botão com spinner de aguardando pagamento
     */
    private function getBtnConfirmPaymentPix(): string
    {
        return "
        <style>
        #confirmPayment {
            position: relative;
            background-color: #f37021;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-top: 3px;
            font-weight: bold;
            width:240px;
          }
          .btn{
            width:240px;
          }
          
          .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.7);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
            vertical-align: middle;
          }
          .fade-out {
            animation-name: fadeOut;
            animation-duration: 0.5s;
            animation-fill-mode: forwards;
          }
         .fade-in {
            animation-name: fadeIn;
            animation-duration: 0.5s;
            animation-fill-mode: forwards;
         }
          
          @keyframes spin {
            0% {
              transform: rotate(0deg);
            }
            100% {
              transform: rotate(360deg);
            }
          }
          @keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
          }
            @keyframes fadeIn {
                0% {
                    opacity: 0;
                }
                100% {
                    opacity: 1;
                }
            }
          
    
    </style>
    
    <button class='btn btn-default' id='confirmPayment'>
      <span id='txtBtn'>Aguardando Pagamento</span>
      <span class='spinner' id='spinnerBtn'></span>
    </button>
    
    
    
    
        ";
    }


    private function getScriptsForPaymentPix()
    {
        $paramsGateway = getGatewayVariables('efi');

        $baseUrl = $paramsGateway['systemurl'];



        // Script for Copy action

        $script = "<script type=\"text/javascript\" src=\"$baseUrl/modules/gateways/efi/gerencianet_lib/scripts/js/copyQrCode.js\"></script>";
        $scriptStatusPix = "<script type=\"text/javascript\" src=\"$baseUrl/modules/gateways/efi/gerencianet_lib/scripts/js/validation/validationPaymentPix.js\"></script>";

        return $script . $scriptStatusPix;
    }
}
