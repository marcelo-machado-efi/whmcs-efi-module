<?php

namespace PaymentGateway\Methods\Boleto;

class Discount
{

    private string $type, $value;

    /**
     * Discount constructor.
     * 
     * @param array $gatewayParams
     */
    public function __construct(array $gatewayParams)
    {
        $this->type = $gatewayParams['tipoDesconto'] == '1' ? 'percentage' : 'currency';
        $this->value = $gatewayParams['descontoBoleto'] == '' ? 0 : $gatewayParams['descontoBoleto'];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): float
    {

        switch ($this->getType()) {
            case 'percentage':
                return ($this->value * 100);
                break;
            case 'currency':
                return $this->valorFormatadoParaCentavos($this->value);
                break;
        }
    }

    private function valorFormatadoParaCentavos($valor)
    {
        $value = number_format((float)$valor, 2, '.', '');

        $value = preg_replace("/[.,-]/", "", $value);
        return $value;
    }

    public function getTotalWithDiscount($total)
    {
        switch ($this->getType()) {
            case 'percentage':
                return $this->totalDiscountPercentage($total);
                break;
            case 'currency':
                return $this->totalDiscountCurrency($total);
                break;
        }
    }

    private function totalDiscountPercentage($total)
    {

        $valorDesconto =  1 - ($this->getValue() / 100);
        $totalParaPagamento  = $total *  $valorDesconto;

        return $totalParaPagamento;
    }
    private function totalDiscountCurrency($total)
    {

        $valorDesconto =   $this->getValue();
        $totalParaPagamento  = $total -  $valorDesconto;

        return $totalParaPagamento;
    }
}
