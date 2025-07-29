<?php

namespace App\Enums;

enum LogEvents: string
{
    // General
    case DEFAULT = "No Event";
    case PREPARE = "Prepare";
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
    case STORING_lIKE = "Storing Like";
    case CREATE_REPORT = "Create Report";
    case DELETE_REPORT = "Delete Report";
    case CREATE_UPVOTE = "Create Upvote";
    case CREATE_DOWNVOTE = "Create Downvote";
    case REMOVE_VOTE = "Remove Vote";
}
