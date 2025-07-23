<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Authentication;
use App\Http\Controllers\UserAccess;
use App\Http\Controllers\Article;
use App\Http\Controllers\Comment;
use App\Http\Controllers\Moderation;
use App\Http\Controllers\Badge;
use App\Http\Controllers\Recruiting;
use App\Http\Controllers\Analytics;

// public
Route::get('/articles/{tenantId}/{articleId}/comments', [Article::class, 'getComments'])->name('article.getComments');
Route::get('/articles/{articleId}', [Article::class, 'getInfo'])->name('article.getInfo');
Route::get('/comments/{commentId}/replies', [Comment::class, 'getReplies'])->name('comment.getReplies');
Route::get('/users/{userId}/comments', [Comment::class, 'getCommenterDetails'])->name('comment.getCommenterDetails');

// Authentication endpoints
Route::prefix('/auth')->group(function () {
    Route::post('/register', [Authentication::class, 'register'])->name('register');
    Route::post('/login', [Authentication::class, 'login'])->name('login');
    Route::post('/forgot-password', [Authentication::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/reset-password', [Authentication::class, 'resetPassword'])->name('reset-password');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/logout', [Authentication::class, 'logout'])->name('logout');
        Route::get('/refresh', [Authentication::class, 'refresh'])->name('refresh');
    });

    Route::middleware(['auth:sanctum', 'role:commenter'])->group(function () {
        Route::get('/me', [UserAccess::class, 'me'])->name('me');
        Route::put('/profile', [UserAccess::class, 'profile'])->name('profile');
        Route::delete('/account', [UserAccess::class, 'deleteAccount'])->name('delete-account');
    });
});

// Public/Semi-public
Route::middleware(['permission:reporting|read comments'])->group(function () {
//    Route::get('/comments/{commentId}/thread', [Comment::class, 'getThread'])->name('comment.getThread');
//    Route::get('/comments/{commentId}/context', [Comment::class, 'getContext'])->name('comment.getContext');
});

Route::middleware(['auth:sanctum', 'permission:create comments|delete comments|update comments'])->group(function () {
    Route::post('/comments/{commentId}/like', [Comment::class, 'addLike'])->name('comment.addLike');
    // ... other routes
});

// Comment creation
Route::middleware(['auth:sanctum', 'permission:create comments|delete comments|update comments'])->group(function () {
    Route::post('/comments/{commentId}/like', [Comment::class, 'addLike'])->name('comment.addLike');
    Route::delete('/comments/{commentId}/like', [Comment::class, 'deleteLike'])->name('comment.deleteLike');
    Route::post('/comments/{commentId}/report', [Comment::class, 'addReport'])->name('comment.addReport');
    Route::delete('/comments/{commentId}/report', [Comment::class, 'deleteReport'])->name('comment.deleteReport');

    Route::post('/comments/{commentId}/upvote', [Comment::class, 'upvote'])->name('comment.upvote');
    Route::post('/comments/{commentId}/downVote', [Comment::class, 'downVote'])->name('comment.downVote');
    Route::delete('/comments/{commentId}/vote', [Comment::class, 'removeVote'])->name('comment.removeVote');

    Route::post('/articles/comments', [Article::class, 'addComment'])->name('article.addComment');
    Route::post('/comments/{commentId}/replies', [Comment::class, 'addReply'])->name('comment.addReply');
});

// Comment editing
Route::middleware(['auth:sanctum', 'permission:update comments'])->group(function () {
    Route::put('/comments/{commentId}', [Comment::class, 'updateComment'])->name('comment.updateComment');
    Route::patch('/comments/{commentId}', [Comment::class, 'patchComment'])->name('comment.patchComment');
});

// Comment deletion
Route::middleware(['auth:sanctum', 'permission:delete comments'])->group(function () {
    Route::delete('/comments/{commentId}', [Comment::class, 'deleteComment'])->name('comment.deleteComment');
    Route::post('/comments/{commentId}/restore', [Comment::class, 'restoreComment'])->name('comment.restoreComment');
});

// Moderation
Route::middleware(['auth:sanctum', 'permission:ban user'])->group(function () {
    Route::post('/moderation/users/{userId}/ban', [Moderation::class, 'banUser'])->name('moderation.banUser');
    Route::delete('/moderation/users/{userId}/ban', [Moderation::class, 'unbanUser'])->name('moderation.unbanUser');
    Route::get('/moderation/users/{userId}/ban-history', [Moderation::class, 'getBanHistory'])->name('moderation.getBanHistory');
});

Route::middleware(['auth:sanctum', 'permission:mute user'])->group(function () {
    Route::post('/moderation/users/{userId}/mute', [Moderation::class, 'muteUser'])->name('moderation.muteUser');
    Route::delete('/moderation/users/{userId}/mute', [Moderation::class, 'unmuteUser'])->name('moderation.unmuteUser');
    Route::get('/moderation/users/{userId}/mute-history', [Moderation::class, 'getMuteHistory'])->name('moderation.getMuteHistory');
});

Route::middleware(['auth:sanctum', 'permission:hide content'])->group(function () {
    Route::post('/comments/{commentId}/hide', [Moderation::class, 'hideComment'])->name('moderation.hideComment');
    Route::delete('/comments/{commentId}/hide', [Moderation::class, 'unHideComment'])->name('moderation.unHideComment');
    Route::post('/comments/{commentId}/approve', [Moderation::class, 'approveComment'])->name('moderation.approveComment');
    Route::post('/comments/{commentId}/flag', [Moderation::class, 'flagComment'])->name('moderation.flagComment');
});

Route::middleware(['auth:sanctum', 'permission:view reports'])->group(function () {
    Route::get('/moderation/reports', [Moderation::class, 'getReports'])->name('moderation.getReports');
    Route::get('/moderation/reports/{reportId}', [Moderation::class, 'getReport'])->name('moderation.getReport');
    Route::post('/moderation/reports/{reportId}/resolve', [Moderation::class, 'resolveReport'])->name('moderation.resolveReport');
    Route::get('/moderation/queue', [Moderation::class, 'getModerationQueue'])->name('moderation.getQueue');
});

// Badges
Route::middleware(['auth:sanctum', 'permission:badging'])->group(function () {
    Route::post('/users/{userId}/badges', [Badge::class, 'awardBadge'])->name('badge.awardBadge');
    Route::delete('/users/{userId}/badges/{badgeId}', [Badge::class, 'revokeBadge'])->name('badge.revokeBadge');
    Route::get('/badges', [Badge::class, 'getBadges'])->name('badge.getBadges');
});

// Recruiting
Route::middleware(['auth:sanctum', 'permission:recruiting'])->group(function () {
    Route::post('/users/{userId}/recruit', [Recruiting::class, 'recruitUser'])->name('recruiting.recruitUser');
    Route::get('/recruiting/invitations', [Recruiting::class, 'getInvitations'])->name('recruiting.getInvitations');
});

// Analytics
Route::middleware(['auth:sanctum', 'permission:view analytics'])->group(function () {
    Route::get('/analytics/comments', [Analytics::class, 'getCommentStats'])->name('analytics.comments');
    Route::get('/analytics/users', [Analytics::class, 'getUserStats'])->name('analytics.users');
    Route::get('/analytics/engagement', [Analytics::class, 'getEngagementStats'])->name('analytics.engagement');
});

// Notification system
//Route::middleware(['auth:sanctum'])->group(function () {
//    Route::get('/notifications', 'Notification@getNotifications')->name('notifications.get');
//    Route::post('/notifications/{notificationId}/read', 'Notification@markAsRead')->name('notifications.markAsRead');
//    Route::post('/notifications/read-all', 'Notification@markAllAsRead')->name('notifications.markAllAsRead');
//    Route::delete('/notifications/{notificationId}', 'Notification@deleteNotification')->name('notifications.delete');
//});

// Health check and system status
//Route::get('/health', 'System@health')->name('system.health');
//Route::get('/status', 'System@status')->name('system.status');

