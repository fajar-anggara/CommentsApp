<?php

namespace App\Enums;

enum Roles: string
{
    case ADMIN = "admin";
    case MODERATOR = "moderator";
    case COMMENTER = "commenter";
    case READER = "reader";
}
