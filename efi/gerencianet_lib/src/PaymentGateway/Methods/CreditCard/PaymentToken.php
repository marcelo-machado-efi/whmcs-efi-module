<?php

namespace PaymentGateway\Methods\CreditCard;

/**
 * Class PaymentToken
 */
class PaymentToken
{
    private bool $reuse = false;
    private string $value;

    /**
     * @param string $value  Valor correspondente ao Token do cartão do cliente final
     * @param bool $reuse  Opção que define se o cartão será reutilizado ou não
     */
    function __construct(string $value, bool $reuse = false)
    {
        $this->value = $value;
        $this->reuse = $reuse;
    }

    /**
     * Obtém o valor do token do cartão do cliente final.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Verifica se o cartão será reutilizado.
     *
     * @return bool
     */
    public function isReuse(): bool
    {
        return $this->reuse;
    }
}
