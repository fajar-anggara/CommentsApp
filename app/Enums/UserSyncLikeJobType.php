<?php

namespace App\Enums;

enum UserSyncLikeJobType
{
    case USER_SYNC_LIKES_INCREMENT;
    case USER_SYNC_LIKES_DECREMENT;
}
