<?php

namespace PaymentGateway\Logging;



class TransactionLogger
{
    const SUCCESS_LOG = 'success';
    const ERROR_LOG = 'error';
    const DEBUG_LOG = 'debug';



    public static function log(string $message, string $typeLog)
    {
        logTransaction('Efi', $message,  $typeLog);
    }
}
