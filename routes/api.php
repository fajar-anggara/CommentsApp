<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication endpoints
Route::prefix("auth")->group(function () {
    Route::post("/register", "AuthenticationDo@register")->name("register");
    Route::post("/login", "AuthenticationDo@login")->name("login");
    Route::post("/logout", "AuthenticationDo@logout")->name("logout");
    Route::post("/refresh", "AuthenticationDo@refresh")->name("refresh"); // Token refresh
    Route::post("/forgot-password", "AuthenticationDo@forgotPassword")->name("forgot-password");
    Route::post("/reset-password", "AuthenticationDo@resetPassword")->name("reset-password");

    Route::group(["middleware" => ["auth:sanctum", "role:commenter"]], function () {
        Route::get("/me", "UserAccess@me")->name("me");
        Route::put("/profile", "UserAccess@profile")->name("profile");
        Route::delete("/account", "UserAccess@deleteAccount")->name("delete-account");
    });
});

// Public/Semi-public routes
Route::group(["middleware" => ["permission:reporting|read comments"]], function () {
    Route::get("/articles/{articleId}", "Article@getInfo")->name("article.getInfo");
    Route::get("/articles/{externalId}/comments", "Article@getComments")->name("article.getComments");

    // Comment interactions
    Route::get("/comments/{commentId}/replies", "Comment@getReplies")->name("comment.getReplies");
    Route::post("/comments/{commentId}/like", "Comment@addLike")->name("comment.addLike");
    Route::delete("/comments/{commentId}/like", "Comment@deleteLike")->name("comment.deleteLike");
    Route::post("/comments/{commentId}/report", "Comment@addReport")->name("comment.addReport");
    Route::delete("/comments/{commentId}/report", "Comment@deleteReport")->name("comment.deleteReport");
    Route::get("/users/{userId}/comments", "Comment@getCommenterDetails")->name("comment.getCommenterDetails");

    // Comment voting/reactions
    Route::post("/comments/{commentId}/upvote", "Comment@upvote")->name("comment.upvote");
    Route::post("/comments/{commentId}/downVote", "Comment@downVote")->name("comment.downVote");
    Route::delete("/comments/{commentId}/vote", "Comment@removeVote")->name("comment.removeVote");

    // Comment threading and pagination
    Route::get("/comments/{commentId}/thread", "Comment@getThread")->name("comment.getThread");
    Route::get("/comments/{commentId}/context", "Comment@getContext")->name("comment.getContext");
});

// Comment creation
Route::middleware(['auth:sanctum', 'permission:create comments'])->group(function () {
    Route::post('/articles/{externalId}/comments', 'Article@addComment')->name('article.addComment');
    Route::post('/comments/{commentId}/replies', 'Comment@addReply')->name('comment.addReply');
});

// Comment editing
Route::middleware(['auth:sanctum', 'permission:update comments'])->group(function () {
    Route::put('/comments/{commentId}', 'Comment@updateComment')->name('comment.updateComment');
    Route::patch('/comments/{commentId}', 'Comment@patchComment')->name('comment.patchComment');
});

// Comment deletion
Route::middleware(['auth:sanctum', 'permission:delete comments'])->group(function () {
    Route::delete('/comments/{commentId}', 'Comment@deleteComment')->name('comment.deleteComment');
    Route::post('/comments/{commentId}/restore', 'Comment@restoreComment')->name('comment.restoreComment');
});

// User moderation
Route::middleware(['auth:sanctum', 'permission:ban user'])->group(function () {
    Route::post('/moderation/users/{userId}/ban', 'Moderation@banUser')->name('moderation.banUser');
    Route::delete('/moderation/users/{userId}/ban', 'Moderation@unbanUser')->name('moderation.unbanUser');
    Route::get('/moderation/users/{userId}/ban-history', 'Moderation@getBanHistory')->name('moderation.getBanHistory');
});

Route::middleware(['auth:sanctum', 'permission:mute user'])->group(function () {
    Route::post('/moderation/users/{userId}/mute', 'Moderation@muteUser')->name('moderation.muteUser');
    Route::delete('/moderation/users/{userId}/mute', 'Moderation@unmuteUser')->name('moderation.unmuteUser');
    Route::get('/moderation/users/{userId}/mute-history', 'Moderation@getMuteHistory')->name('moderation.getMuteHistory');
});

// Content moderation
Route::middleware(['auth:sanctum', 'permission:hide content'])->group(function () {
    Route::post('/comments/{commentId}/hide', 'Moderation@hideComment')->name('moderation.hideComment');
    Route::delete('/comments/{commentId}/hide', 'Moderation@unHideComment')->name('moderation.unHideComment');
    Route::post('/comments/{commentId}/approve', 'Moderation@approveComment')->name('moderation.approveComment');
    Route::post('/comments/{commentId}/flag', 'Moderation@flagComment')->name('moderation.flagComment');
});

// Reports and moderation queue
Route::middleware(['auth:sanctum', 'permission:view reports'])->group(function () {
    Route::get('/moderation/reports', 'Moderation@getReports')->name('moderation.getReports');
    Route::get('/moderation/reports/{reportId}', 'Moderation@getReport')->name('moderation.getReport');
    Route::post('/moderation/reports/{reportId}/resolve', 'Moderation@resolveReport')->name('moderation.resolveReport');
    Route::get('/moderation/queue', 'Moderation@getModerationQueue')->name('moderation.getQueue');
});

// Badge system
Route::middleware(['auth:sanctum', 'permission:badging'])->group(function () {
    Route::post('/users/{userId}/badges', 'Badge@awardBadge')->name('badge.awardBadge');
    Route::delete('/users/{userId}/badges/{badgeId}', 'Badge@revokeBadge')->name('badge.revokeBadge');
    Route::get('/badges', 'Badge@getBadges')->name('badge.getBadges');
});

// Recruiting system
Route::middleware(['auth:sanctum', 'permission:recruiting'])->group(function () {
    Route::post('/users/{userId}/recruit', 'Recruiting@recruitUser')->name('recruiting.recruitUser');
    Route::get('/recruiting/invitations', 'Recruiting@getInvitations')->name('recruiting.getInvitations');
});

// Analytics and statistics
Route::middleware(['auth:sanctum', 'permission:view analytics'])->group(function () {
    Route::get('/analytics/comments', 'Analytics@getCommentStats')->name('analytics.comments');
    Route::get('/analytics/users', 'Analytics@getUserStats')->name('analytics.users');
    Route::get('/analytics/engagement', 'Analytics@getEngagementStats')->name('analytics.engagement');
});

// Notification system
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/notifications', 'Notification@getNotifications')->name('notifications.get');
    Route::post('/notifications/{notificationId}/read', 'Notification@markAsRead')->name('notifications.markAsRead');
    Route::post('/notifications/read-all', 'Notification@markAllAsRead')->name('notifications.markAllAsRead');
    Route::delete('/notifications/{notificationId}', 'Notification@deleteNotification')->name('notifications.delete');
});

// Health check and system status
//Route::get('/health', 'System@health')->name('system.health');
//Route::get('/status', 'System@status')->name('system.status');

