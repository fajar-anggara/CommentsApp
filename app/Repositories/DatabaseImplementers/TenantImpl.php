<?php

namespace App\Repositories\DatabaseImplementers;

use App\Enums\LogEvents;
use App\Exceptions\TenantExceptions\TenantNotFoundException;
use App\Facades\SetLog;
use App\Models\Tenant;
use App\Repositories\Interfaces\TenantRepository;



class TenantImpl implements TenantRepository
{
    public function findTenantById(int $id): ?Tenant
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            throw new TenantNotFoundException(
                "Tenant tidak ditemukan",
                [
                    'id' => $id,
                    'model' => Tenant::class
                ]
            );
        }

        SetLog::withEvent(LogEvents::FETCHING)
            ->causedBy($tenant)
            ->performedOn($tenant)
            ->withProperties([
                'name' => $tenant->name,
                'domain' => $tenant->domain,
                'tenant_id' => $tenant->id,
                'user_id' => $tenant->user_id,
            ]);

        return $tenant;
    }
}
