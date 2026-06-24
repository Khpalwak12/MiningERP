<?php

namespace App\Services;

use App\Contracts\Repositories\MarbleShipmentRepositoryInterface;
use App\Models\MarbleShipment;
use App\Services\Concerns\ManagesFinancialYear;
use Illuminate\Support\Facades\DB;

class MarbleShipmentService
{
    use ManagesFinancialYear;
    public function __construct(private MarbleShipmentRepositoryInterface $repository) {}

    public function paginate(array $filters = [])
    {
        return $this->repository->paginate($filters['per_page'] ?? 15, $filters);
    }

    public function create(array $data): MarbleShipment
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();
            $data = $this->assignActiveFinancialYear($data);

            return $this->repository->create($data);
        });
    }

    public function update(MarbleShipment $shipment, array $data): MarbleShipment
    {
        $this->ensureFinancialYearWritable($shipment);

        return DB::transaction(fn () => $this->repository->update($shipment, $data));
    }

    public function delete(MarbleShipment $shipment): bool
    {
        $this->ensureFinancialYearWritable($shipment);

        return DB::transaction(fn () => $this->repository->delete($shipment));
    }
}
