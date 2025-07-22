<?php

namespace App\Http\Controllers;

use App\Enums\LogEvents;
use App\Facades\CommentDo;
use App\Facades\SetLog;
use App\Http\Requests\CommentsAddRequest;
use Illuminate\Http\JsonResponse;
use App\Facades\Article as ArticleDo;

class Article
{

    public function getInfo()
    {

    }

    public function getComments()
    {

    }

    /**
     * Add a new comment to an article
     *
     * @OA\Post(
     *     path="/api/articles/{externalId}/comments",
     *     summary="Add a new comment to an article",
     *     tags={"Articles"},
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

        return response()->json([
            'success' => true,
            'message' => 'Berhasil comment',
        ]);
    }
}
