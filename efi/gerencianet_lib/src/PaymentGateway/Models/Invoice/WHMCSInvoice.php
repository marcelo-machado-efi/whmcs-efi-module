<?php

namespace PaymentGateway\Models\Invoice;

use PaymentGateway\Models\Invoice\InvoiceItem;
use PaymentGateway\Logging\TransactionLogger;


class WHMCSInvoice
{
    private string $result;
    private int $invoiceId;
    private string $invoiceNum;
    private int $userId;
    private string $date;
    private string $dueDate;
    private string $datePaid;
    private string $lastCaptureAttempt;
    private float $subtotal;
    private float $credit;
    private float $tax;
    private float $tax2;
    private float $total;
    private float $balance;
    private float $taxRate;
    private float $taxRate2;
    private string $status;
    private string $paymentMethod;
    private string $notes;
    private bool $ccGateway;
    private array $items = [];
    private array $transactions = [];


    public function __construct(int $invoiceId)

    {
        $command = 'GetInvoice';
        $postData = [
            'invoiceid' => $invoiceId
        ];
        $getInvoiceApiWhmcs =  localAPI($command, $postData);
        $this->mapResponse($getInvoiceApiWhmcs);
    }

    // Mapeia os dados da resposta para as propriedades da classe
    private function mapResponse(array $data): void
    {
        try {
            if (isset($data['result']) && $data['result'] === 'success') {
                $this->result = $data['result'];
                $this->invoiceId = (int)$data['invoiceid'];
                $this->invoiceNum = $data['invoicenum'] ?? '';
                $this->userId = (int)$data['userid'];
                $this->date = $data['date'] ?? '';
                $this->dueDate = $data['duedate'] ?? '';
                $this->datePaid = $data['datepaid'] ?? '';
                $this->lastCaptureAttempt = $data['lastcaptureattempt'] ?? '';
                $this->subtotal = (float)($data['subtotal'] ?? 0.0);
                $this->credit = (float)($data['credit'] ?? 0.0);
                $this->tax = (float)($data['tax'] ?? 0.0);
                $this->tax2 = (float)($data['tax2'] ?? 0.0);
                $this->total = (float)($data['total'] ?? 0.0);
                $this->balance = (float)($data['balance'] ?? 0.0);
                $this->taxRate = (float)($data['taxrate'] ?? 0.0);
                $this->taxRate2 = (float)($data['taxrate2'] ?? 0.0);
                $this->status = $data['status'] ?? '';
                $this->paymentMethod = $data['paymentmethod'] ?? '';
                $this->notes = $data['notes'] ?? '';
                $this->ccGateway = isset($data['ccgateway']) ? (bool)$data['ccgateway'] : false;

                // Mapeia os itens de fatura
                if (isset($data['items']['item']) && is_array($data['items']['item'])) {
                    foreach ($data['items']['item'] as $item) {
                        $this->items[] = new InvoiceItem($item);
                    }
                }

                // Mapeia as transações
                if (isset($data['transactions']['transaction']) && is_array($data['transactions']['transaction'])) {
                    $this->transactions = $data['transactions']['transaction'];
                } else {
                    $this->transactions = [];
                }
            } else {
                TransactionLogger::log("Erro na resposta da API: " . ($data['result'] ?? 'Resposta inválida'), TransactionLogger::ERROR_LOG);
            }
        } catch (\Throwable $th) {
            TransactionLogger::log(json_encode($th->getMessage()), TransactionLogger::ERROR_LOG);
        }
    }


    // Métodos getters para acessar as propriedades
    public function getResult(): string
    {
        return $this->result;
    }

    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    public function getInvoiceNum(): string
    {
        return $this->invoiceNum;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getDueDate(): string
    {
        return $this->dueDate;
    }

    public function getDatePaid(): string
    {
        return $this->datePaid;
    }

    public function getLastCaptureAttempt(): string
    {
        return $this->lastCaptureAttempt;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getCredit(): float
    {
        return $this->credit;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function getTax2(): float
    {
        return $this->tax2;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    public function getTaxRate2(): float
    {
        return $this->taxRate2;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function getCcGateway(): bool
    {
        return $this->ccGateway;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getTransactions(): array
    {
        return $this->transactions ?? [];
    }
}
