<?php

namespace App\Helpers;

use App\Exceptions\TenantExceptions\TenantNotFoundException;
use App\Models\Tenant;
use App\Repositories\Interfaces\TenantRepository;

class TenantHelper
{
    public function __construct(
        protected TenantRepository $tenantRepository
    ) {}

    public function findById(string $tenantId): Tenant
    {
        $tenant = $this->tenantRepository->findById($tenantId);

        if (!$tenant) {
            throw new TenantNotFoundException(
                "Tenant tidak ditemukan",
                [
                    'tenant_id' => $tenantId,
                    'model' => Tenant::class
                ]
            );
        }

        return $tenant;
    }
}
