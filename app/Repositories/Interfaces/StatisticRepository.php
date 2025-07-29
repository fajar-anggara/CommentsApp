<?php

namespace App\Repositories\Interfaces;

use App\Models\StatisticUser;

interface StatisticRepository
{
    public function initiate(
        string $commenterId
    ): StatisticUser|bool;
}
