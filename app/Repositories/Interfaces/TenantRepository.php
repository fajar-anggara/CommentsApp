<?php

namespace App\Repositories\Interfaces;

use App\Models\Tenant;

interface TenantRepository
{
    public function findTenantById(int $id): ?Tenant;
}
