<?php

namespace PaymentGateway\Methods\Pix;

class Discount
{

    private string  $value;

    /**
     * Discount constructor.
     * 
     * @param array $gatewayParams
     */
    public function __construct(array $gatewayParams)
    {
        $this->value = $gatewayParams['pixDiscount'] == '' ? 0 : $gatewayParams['pixDiscount'];
    }



    public function getValue(): float
    {

        return (float) $this->value;
    }

    private function valorFormatadoParaCentavos($valor)
    {
        $value = number_format((float)$valor, 2, '.', '');

        $value = preg_replace("/[.,-]/", "", $value);
        return $value;
    }

    public function getTotalWithDiscount($total)
    {
        $totalParaPagamento = $total;

        if ($this->getValue() > 0) {
            $valorDesconto =  1 - ($this->getValue() / 100);
            $totalParaPagamento  = $totalParaPagamento *  $valorDesconto;
        }


        return $totalParaPagamento;
    }
}
