<?php



namespace PaymentGateway\DTOs\Config;

use Exception;

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
        $config = [];
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

        $config["headers"] = [
            'x-skip-mtls-checking' => $this->mtls ? 'false' : 'true', // Needs to be string
            'x-idempotency-key' => uniqid($this->uniqidReal(), true), // For open finance usage
            'efi-whmcs-version' => $this->version
        ];
        return $config;
    }

    /**
     * Generates an random prefix to compose a unique ID for
     * open finance idempotency key.
     */
    private function uniqidReal($lenght = 30)
    {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }
}
