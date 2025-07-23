<?php

namespace App\Enums;

enum StatisticArticleJobType: string
{
    case INCREMENT_VIEWS = 'increment_views';
    case INCREMENT_COMMENTS_COUNT = 'increment_comments_count';
    case DECREMENT_COMMENTS_COUNT = 'decrement_comments_count';
    case UPDATE_LAST_ACTIVITY = 'update_last_activity';
}
