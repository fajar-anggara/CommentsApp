<?php

namespace App\Enums;

enum StatisticUserJobType: string
{
    case INCREMENT_COMMENTS_CREATED = 'increment_comments_created';
    case INITIALIZE_USER_STATISTICS = 'initialize_user_statistics';
    case INCREMENT_UPVOTE_GIVEN = 'increment_upvote_given';
    case INCREMENT_DOWNVOTE_GIVEN = 'increment_downvote_given';
    case INCREMENT_LIKES_GIVEN = 'increment_likes_given';
    case INCREMENT_REPORTS_MADE = 'increment_reports_made';
    case DECREMENT_COMMENTS_CREATED = 'decrement_comments_created';
    case DECREMENT_UPVOTE_GIVEN = 'decrement_upvote_given';
    case DECREMENT_DOWNVOTE_GIVEN = 'decrement_downvote_given';
    case DECREMENT_LIKES_GIVEN = 'decrement_likes_given';
    case DECREMENT_REPORTS_MADE = 'decrement_reports_made';
}
