<?php

namespace App\Services;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Models\Customer;
use App\Support\JalaliDate;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function __construct(private CustomerRepositoryInterface $repository) {}

    public function paginate(array $filters = [])
    {
        return $this->repository->withBalances($filters);
    }

    public function create(array $data): Customer
    {
        return DB::transaction(fn () => $this->repository->create($data));
    }

    public function update(Customer $customer, array $data): Customer
    {
        return DB::transaction(fn () => $this->repository->update($customer, $data));
    }

    public function delete(Customer $customer): bool
    {
        return DB::transaction(fn () => $this->repository->delete($customer));
    }

    public function ledger(Customer $customer)
    {
        $shipments = $customer->shipments()->latest('shipment_date')->get()->map(fn ($s) => [
            'date' => $s->shipment_date->format('Y-m-d'),
            'date_shamsi' => JalaliDate::fromGregorian($s->shipment_date),
            'type' => 'sale',
            'description' => __('erp.shipments.title').' #'.$s->id.($s->isCompleted() ? '' : ' ('.__('erp.shipments.statuses.'.$s->status).')'),
            'debit' => $s->isCompleted() ? (float) $s->total_amount : 0,
            'credit' => 0,
        ]);

        $payments = $customer->payments()->latest('payment_date')->get()->map(fn ($p) => [
            'date' => $p->payment_date->format('Y-m-d'),
            'date_shamsi' => JalaliDate::fromGregorian($p->payment_date),
            'type' => 'payment',
            'description' => __('erp.payments.title').' #'.$p->id,
            'debit' => 0,
            'credit' => (float) $p->amount,
        ]);

        return $shipments->concat($payments)->sortByDesc('date')->values();
    }
}
