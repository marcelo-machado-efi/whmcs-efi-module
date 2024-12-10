<?php

namespace PaymentGateway\Models\Invoice;

use PaymentGateway\Logging\TransactionLogger;

class InvoiceItem
{
    private int $id;
    private string $type;
    private int $relid;
    private string $description;
    private float $amount;
    private bool $taxed;

    // Construtor que mapeia os dados de cada item da fatura
    public function __construct(array $itemData)
    {
        try {
            $this->id = $itemData['id'];
            $this->type = $itemData['type'];
            $this->relid = $itemData['relid'];
            $this->description = $itemData['description'];
            $this->amount = (float)$itemData['amount'];
            $this->taxed = (bool)$itemData['taxed'];
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
        }
    }

    // Métodos getters para acessar os dados do item
    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getRelid(): int
    {
        return $this->relid;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getTaxed(): bool
    {
        return $this->taxed;
    }
}
