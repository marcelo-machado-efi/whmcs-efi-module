<?php



namespace PaymentGateway\DTOs\Config;



class ConfigAPIDTO
{
    public string $clientIdProduction, $clientIdSandbox, $clientSecretProduction, $clientSecretSandBox, $certificate;
    public bool $sandbox, $debug, $mtls;
    public int $timeout;
    public float  $version;

    function __construct(array $gatewayParams)
    {
        $this->clientIdProduction = $gatewayParams['clientIdProd'];
        $this->clientIdSandbox = $gatewayParams['clientIdSandbox'];
        $this->clientSecretProduction = $gatewayParams['clientSecretProd'];
        $this->clientSecretSandBox = $gatewayParams['clientSecretSandbox'];
        $this->certificate = $gatewayParams['pixCert'];
        $this->sandbox = ($gatewayParams['sandbox'] == 'on');
        $this->debug = ($gatewayParams['debug'] == 'on');
        $this->mtls = ($gatewayParams['mtls'] == 'on');
        $this->timeout = 60;
        $this->version = 2.4;
    }
    public function getApiConfig(): array
    {
        if (!$this->sandbox) {
            $config = [
                "clientId" => $this->clientIdProduction,
                "clientSecret" => $this->clientSecretProduction,
                "certificate" => $this->certificate,
                "sandbox" => $this->sandbox,
                "debug" => $this->debug,
                "timeout" => $this->timeout,
            ];
        } else {
            $config = [
                "clientId" => $this->clientIdSandbox,
                "clientSecret" => $this->clientSecretSandBox,
                "certificate" => $this->certificate,
                "sandbox" => $this->sandbox,
                "debug" => $this->debug,
                "timeout" => $this->timeout,
            ];
        }
        return $config;
    }
}
