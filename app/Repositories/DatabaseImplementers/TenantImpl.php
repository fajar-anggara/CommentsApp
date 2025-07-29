<?php

namespace App\Repositories\DatabaseImplementers;

use App\Enums\LogEvents;
use App\Exceptions\TenantExceptions\TenantNotFoundException;
use App\Facades\SetLog;
use App\Models\Tenant;
use App\Repositories\Interfaces\TenantRepository;



class TenantImpl implements TenantRepository
{
    public function findById(int $id): Tenant|bool
    {
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return false;
        }

        return $tenant;
    }
}
