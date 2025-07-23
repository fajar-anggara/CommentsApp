<?php

namespace App\Enums;

enum QueueBucket: string
{
    case STATISTICS = 'statistics';
    case LOGS = 'logs';
}
