<?php

namespace App\Repositories\Interfaces;

use App\Models\Tenant;

interface TenantRepository
{
    public function findById(
        int $id
    ): Tenant|bool;
}
