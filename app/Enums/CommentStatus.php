<?php

namespace App\Enums;

enum CommentStatus: string
{
    case PUBLISHED = 'published';
    case HIDDEN = 'hidden';
    case DELETED = 'deleted';
}
