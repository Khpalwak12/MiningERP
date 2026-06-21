<?php

namespace App\Services;

use App\Contracts\Repositories\MarbleShipmentRepositoryInterface;
use App\Models\MarbleShipment;
use Illuminate\Support\Facades\DB;

class MarbleShipmentService
{
    public function __construct(private MarbleShipmentRepositoryInterface $repository) {}

    public function paginate(array $filters = [])
    {
        return $this->repository->paginate($filters['per_page'] ?? 15, $filters);
    }

    public function create(array $data): MarbleShipment
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = auth()->id();

            return $this->repository->create($data);
        });
    }

    public function update(MarbleShipment $shipment, array $data): MarbleShipment
    {
        return DB::transaction(fn () => $this->repository->update($shipment, $data));
    }

    public function delete(MarbleShipment $shipment): bool
    {
        return DB::transaction(fn () => $this->repository->delete($shipment));
    }
}
