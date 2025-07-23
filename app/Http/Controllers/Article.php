<?php

namespace App\Http\Controllers;

use App\Enums\LogEvents;
use App\Enums\StatisticArticleJobType;
use App\Facades\CommentDo;
use App\Facades\Fractal;
use App\Facades\SetLog;
use App\Http\Requests\CommentsAddRequest;
use App\Jobs\addStatisticArticle;
use Illuminate\Http\JsonResponse;
use App\Facades\Article as ArticleDo;

class Article
{

    public function getInfo()
    {

    }

    /**
     * Get the comments
     *
     * @OA\Get (
     *     path="/api/articles/{tenantId}/{externalId}/comments",
     *     summary="Get all comments in the article based on tenant_id external_article_id",
     *     tags={"Article"},
     *     @OA\Parameter (
     *         name="tenantId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Tenant id"
     *     ),
     *     @OA\Parameter (
     *          name="externalId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string"),
     *          description="The external id of the article"
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Success to fetch comments",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Berhasil memuat komentar"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="0198326e-df9d-71ea-b271-fac6c3052657"),
     *                     @OA\Property(property="article_id", type="integer", example=1),
     *                     @OA\Property(property="tenant_id", type="string", example="1"),
     *                     @OA\Property(property="user_id", type="string", example="01982faf-bf2e-7050-8c0a-cdef6db19460"),
     *                     @OA\Property(property="content", type="string", example="This is a great comment!"),
     *                     @OA\Property(property="parent_id", type="string", nullable=true, example=null),
     *                     @OA\Property(property="status", type="string", example="approved"),
     *                     @OA\Property(property="likes_count", type="integer", example=0),
     *                     @OA\Property(property="reports_count", type="integer", example=0),
     *                     @OA\Property(property="upvotes_count", type="integer", example=0),
     *                     @OA\Property(property="downvotes_count", type="integer", example=0),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-22T21:17:27+07:00"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-22T21:17:27+07:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function getComments($tenantId, $externalId): JsonResponse
    {
        SetLog::withEvent(LogEvents::FETCHING_COMMENTS)
            ->withProperties([
                'performedOn' => [
                    'class' => Article::class,
                    'method' => 'getComments',
                ]
            ])
            ->withMessage("Prepare to get comments");

        $comments = CommentDo::findCommentByExternalArticleIdAndTenantId(
            $externalId,
            $tenantId,
            "0198326e-df9d-71ea-b271-fac6c3052657"
        );

        $data = Fractal::useCommentTransformer($comments)
            ->buildWithArraySerializer();

        SetLog::withEvent(LogEvents::FETCHING)
            ->causedBy($data)
            ->performedOn($data)
            ->withProperties([
                'performedOn' => [
                    'class' => Article::class,
                    'method' => 'getComments',
                ]
            ])
            ->withMessage("Fetch comments successfully");

        addStatisticArticle::dispatch($externalId, StatisticArticleJobType::INCREMENT_VIEWS);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat komentar',
            'data' => $data
        ]);
    }

    /**
     * Add a new comment to an article
     *
     * @OA\Post(
     *     path="/api/articles/{externalId}/comments",
     *     summary="Add a new comment to an article",
     *     tags={"Article"},
     *     @OA\Parameter(
     *         name="externalId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="The external ID of the article"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "content", "tenant_id", "article_id", "article_url"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="content", type="string", example="This is a great article!"),
     *             @OA\Property(property="parent_id", type="integer", example=null),
     *             @OA\Property(property="tenant_id", type="string", example="some-tenant-id"),
     *             @OA\Property(property="article_id", type="string", example="ext-123"),
     *             @OA\Property(property="article_url", type="string", example="http://example.com/article/123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Berhasil comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function addComment(CommentsAddRequest $request): JsonResponse
    {
        $commenter = auth()->guard()->user();
        SetLog::withEvent(LogEvents::CREATE_COMMENTS)
            ->causedBy($commenter)
            ->withProperties([
                'performedOn' => [
                    'class' => Article::class,
                    'method' => 'addComment',
                ]
            ])
            ->withMessage("Prepare to add a comment");
        $validated = $request->validated();

        $article = ArticleDo::findOrCreateByArticleExternalId(
            $validated['article_url'],
            $validated['article_id'],
            $validated['tenant_id'],
        );

        $comment = CommentDo::addNewComment(
            $validated,
            $article->id,
            $commenter,
        );


        SetLog::withEvent(LogEvents::CREATE_COMMENTS)
            ->causedBy($commenter)
            ->performedOn($comment)
            ->withProperties([
                'performedOn' => [
                    'class' => Article::class,
                    'method' => 'addComment',
                ]
            ])
            ->withMessage("Comment added successfully");

        addStatisticArticle::dispatch($article->id, StatisticArticleJobType::INCREMENT_COMMENTS_COUNT);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil comment',
        ]);
    }
}
