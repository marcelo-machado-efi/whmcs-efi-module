<?php

require_once __DIR__ . '/../../enums/EnumBillingCycle.php';

use WHMCS\Database\Capsule;


class InvoiceProcessor
{
    private int $invoiceId;

    public function __construct(int $invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function processRecurringItems(): array
    {
        $recurringItems = Capsule::table('tblinvoiceitems')
            ->where('invoiceid', $this->invoiceId)
            ->whereIn('type', ['Hosting', 'Domain', 'Addon', 'Product'])
            ->get();

        $itemDetails = [];

        foreach ($recurringItems as $item) {
            $type = $item->type;
            $relid = $item->relid; // ID relacionado ao serviço/produto
            $billingCycle = $this->getBillingCycle($type, $relid);

            if ($billingCycle != null) {
                $itemDetails[] = [
                    'item_id' => $item->id,
                    'type' => $type,
                    'billing_cycle' => $billingCycle,
                    'amount' => $item->amount,
                    'description' => $item->description,
                    'relid'=> $relid
                ];
            }
            
        }

        return [
            'item_details' => $itemDetails
        ];
    }

    private function getBillingCycle(string $type, int $relid): ?string
    {
        switch ($type) {
            case 'Hosting':
            case 'Product':
                $billingCycle = Capsule::table('tblhosting')->where('id', $relid)->value('billingcycle');
                return $this->mapBillingCycle($billingCycle);

            case 'Addon':
                $billingCycle = Capsule::table('tblhostingaddons')->where('id', $relid)->value('billingcycle');
                return $this->mapBillingCycle($billingCycle);

            case 'Domain':
                $registrationPeriod = Capsule::table('tbldomains')->where('id', $relid)->value('registrationperiod');
                return $this->mapDomainRegistrationPeriod($registrationPeriod);

            default:
                return null;
        }
    }
    private function mapBillingCycle(?string $billingCycle): ?string
    {
        return BillingCycle::tryFrom($billingCycle)?->value ?? null;
    }

    private function mapDomainRegistrationPeriod(string $registrationPeriod): ?string
    {
        return DomainRegistrationPeriod::tryFrom($registrationPeriod)?->value;
    }


    public function areAllItemsRecurring(): bool
    {
        $result = $this->hasNonRecurringItems();
      
        return empty($result['non_recurring_items']);
    }

    public function hasNonRecurringItems(): array
    {
        $nonRecurringItems = Capsule::table('tblinvoiceitems')
            ->where('invoiceid', $this->invoiceId)
            ->get();


        $itemsData = [];

        foreach ($nonRecurringItems as $item) {
            $type = $item->type;
            $relid = $item->relid; // ID relacionado ao serviço/produto
            $billingCycle = $this->getBillingCycle($type, $relid);
            if ($billingCycle == null) {
                $itemsData[] = [
                    'item_id' => $item->id,
                    'type' => $item->type,
                    'billing_cycle' => null,  // Non-recurring items don't have a billing cycle
                    'amount' => $item->amount,
                    'description' => $item->description,
                ];
            }
        }


        return [
            'non_recurring_items' => $itemsData
        ];
    }
}
