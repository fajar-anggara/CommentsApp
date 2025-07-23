<?php

namespace App\Enums;

enum LogEvents: string
{
    // General
    case DEFAULT = "No Event";
    case FETCHING = "Fetching";
    case FETCHING_COMMENTER = "Fetching Commenter";
    case STORING = "Storing";

    // Authentication
    case REGISTER = "Register";
    case LOGIN = "Login";
    case LOGOUT = "Logout";
    case UPDATE = "Update";
    case DELETE = "Delete";
    case REFRESH_TOKEN = "Refresh Token";

    // Comments
    case CREATE_COMMENTS = "Create Comments";
    case FETCHING_COMMENTS = "Fetching Comments";
    case STORING_COMMENTS = "Storing Comments";
}
