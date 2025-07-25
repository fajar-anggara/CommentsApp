<?php

namespace App\Http\Controllers;

use App\Enums\LogEvents;
use App\Exceptions\FailedToSavedException;
use App\Facades\CommentDo;
use App\Facades\Fractal;
use App\Facades\SetLog;
use App\Http\Requests\CommentAddRepotRequest;
use App\Jobs\ActivityLogJob;
use App\Models\CommentLike;
use App\Models\CommentReport;
use Illuminate\Http\JsonResponse;

class Comment
{
    /**
     * @OA\Get(
     *     path="/comments/{commentId}/replies",
     *     summary="Get replies for a comment",
     *     description="Get replies for a comment",
     *     operationId="getReplies",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         description="Comment's ID",
     *         in="path",
     *         name="commentId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success to fetch comments",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Berhasil memuat komentar"),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="string", example="0198326e-df9d-71ea-b271-fac6c3052657"),
     *                      @OA\Property(property="article_id", type="integer", example=1),
     *                      @OA\Property(property="tenant_id", type="string", example="1"),
     *                      @OA\Property(property="user_id", type="string", example="01982faf-bf2e-7050-8c0a-cdef6db19460"),
     *                      @OA\Property(property="content", type="string", example="This is a great comment!"),
     *                      @OA\Property(property="parent_id", type="string", nullable=true, example=null),
     *                      @OA\Property(property="status", type="string", example="approved"),
     *                      @OA\Property(property="likes_count", type="integer", example=0),
     *                      @OA\Property(property="reports_count", type="integer", example=0),
     *                      @OA\Property(property="upvotes_count", type="integer", example=0),
     *                      @OA\Property(property="downvotes_count", type="integer", example=0),
     *                      @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-22T21:17:27+07:00"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-22T21:17:27+07:00")
     *                  )
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Komentar tidak ditemukan",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Komentar tidak ditemukan")
     *          )
     *      ),
     *
     * )
     */
    public function getReplies(string $commentId): JsonResponse
    {
        SetLog::withEvent(LogEvents::FETCHING_COMMENTS)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'getReplies'
                ]
            ])
            ->withMessage('Prefare to get replies')
            ->build();

        $comments = CommentDo::findRepliesByCommentId($commentId);

        $data = Fractal::useCommentTransformer($comments)
            ->buildWithArraySerializer();

        SetLog::withEvent(LogEvents::FETCHING_COMMENTS)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'getReplies'
                ]
            ])
            ->withMessage('Successfully fetched replies')
            ->build();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat balasan komentar',
            'data' => $data
        ]);
    }


    /**
     * @OA\Post(
     *     path="/comments/{commentId}/like",
     *     summary="Add like to comment",
     *     description="Add like to comment",
     *     operationId="addLike",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         description="Comment's ID",
     *         in="path",
     *         name="commentId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success to add like",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Berhasil menambah like")
     *          )
     *      ),
     *     @OA\Response(
     *          response=404,
     *          description="Komentar tidak ditemukan",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Komentar tidak ditemukan")
     *          )
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Gagal menyimpan like. Harap coba lagi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Gagal menyimpan like. Harap coba lagi")
     *          )
     *      ),
     * )
     */
    public function addLike(string $commentId): JsonResponse
    {
        SetLog::withEvent(LogEvents::STORING)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'addLike'
                ]
            ])
            ->withMessage('Prefare to add like')
            ->build();

        $commenter = auth()->guard()->user();
        $comment = CommentDo::addLikeByCommenter($commentId, $commenter);

        if (!$comment) {
            throw new FailedToSavedException(
                "Gagal menyimpan like. Harap coba lagi",
                [
                    'user' => $commenter,
                    'comment' => $comment,
                    'model' => CommentLike::class
                ]
            );
        }

        ActivityLogJob::dispatch(
            LogEvents::STORING,
            $commenter,
            new \App\Models\Comment(),
            [
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'addLike'
                ]
            ],
            'Berhasil menambah like'
        );

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambah like'
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/comments/{commentId}/like",
     *     summary="Delete like from comment",
     *     description="Delete like from comment",
     *     operationId="deleteLike",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         description="Comment's ID",
     *         in="path",
     *         name="commentId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success to delete like",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Berhasil menghapus like")
     *          )
     *      ),
     *     @OA\Response(
     *          response=404,
     *          description="Komentar tidak ditemukan",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Komentar tidak ditemukan")
     *          )
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Gagal menghapus like. Harap coba lagi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Gagal menghapus like. Harap coba lagi")
     *          )
     *      ),
     * )
     */
    public function deleteLike(string $commentId)
    {
        SetLog::withEvent(LogEvents::DELETE)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'deleteLike'
                ]
            ])
            ->withMessage('Prefare to delete like')
            ->build();

        $commenter = auth()->guard()->user();
        $comment = CommentDo::deleteLikeByCommenter($commentId, $commenter);

        if (!$comment) {
            throw new FailedToSavedException(
                "Gagal menghapus like. Harap coba lagi",
                [
                    'user' => $commenter,
                    'comment' => $comment,
                    'model' => CommentLike::class
                ]
            );
        }

        ActivityLogJob::dispatch(
            LogEvents::DELETE,
            $commenter,
            new \App\Models\Comment(),
            [
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'deleteLike'
                ]
            ],
            'Berhasil menghapus like'
        );

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus like'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/comments/{commentId}/report",
     *     summary="Report comment",
     *     description="Report comment",
     *     operationId="addReport",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         description="Comment's ID",
     *         in="path",
     *         name="commentId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request body",
     *         @OA\JsonContent(
     *              type="object",
     *              required={"reason"},
     *              @OA\Property(property="reason", type="string", example="Komentar spam"),
     *              @OA\Property(property="description", type="string", example="Komentar spam")
     *         ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success to report comment",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Berhasil melaporkan komentar")
     *          )
     *      ),
     *     @OA\Response(
     *          response=404,
     *          description="Komentar tidak ditemukan",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Komentar tidak ditemukan")
     *          )
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Gagal melaporkan komentar. Harap coba lagi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Gagal melaporkan komentar. Harap coba lagi")
     *          )
     *      ),
     * )
     */
    public function addReport(string $commentId, CommentAddRepotRequest $request)
    {
        SetLog::withEvent(LogEvents::STORING)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'addReport'
                ]
            ])
            ->withMessage('Prefare to add report')
            ->build();

        $validated = $request->validated();
        $commenter = auth()->guard()->user();
        $comment = CommentDo::addReportByCommenter($commentId, $commenter, $validated);

        if (!$comment) {
            throw new FailedToSavedException(
                "Gagal menyimpan laporan. Harap coba lagi",
                [
                    'user' => $commenter,
                    'comment' => $comment,
                    'model' => CommentReport::class
                ]
            );
        }

        ActivityLogJob::dispatch(
            LogEvents::STORING,
            $commenter,
            new \App\Models\Comment(),
            [
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'addReport'
                ]
            ],
            'Berhasil mereport komentar'
        );

        return response()->json(
            [
                'success' => true,
                'message' => 'Berhasil mereport komentar'
            ]
        );
    }

    /**
     * @OA\Delete(
     *     path="/comments/{commentId}/report",
     *     summary="Delete report comment",
     *     description="Delete report comment",
     *     operationId="deleteReport",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         description="Comment's ID",
     *         in="path",
     *         name="commentId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success to delete report comment",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Berhasil menghapus report komentar")
     *          )
     *      ),
     *     @OA\Response(
     *          response=404,
     *          description="Komentar tidak ditemukan",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Komentar tidak ditemukan")
     *          )
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Gagal menghapus report komentar. Harap coba lagi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Gagal menghapus report komentar. Harap coba lagi")
     *          )
     *      ),
     * )
     */
    public function deleteReport(string $commentId)
    {
        SetLog::withEvent(LogEvents::DELETE)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'deleteReport'
                ]
            ])
            ->withMessage('Prefare to delete report')
            ->build();

        $commenter = auth()->guard()->user();
        $comment = CommentDo::deleteReportByCommenter($commentId, $commenter);

        if (!$comment) {
            throw new FailedToSavedException(
                "Gagal menghapus report komentar. Harap coba lagi",
                [
                    'user' => $commenter,
                    'comment' => $comment,
                    'model' => CommentReport::class
                ]
            );
        }

        ActivityLogJob::dispatch(
            LogEvents::DELETE,
            $commenter,
            new \App\Models\Comment(),
            [
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'deleteReport'
                ]
            ],
            'Berhasil menghapus report komentar'
        );

        return response()->json(
            [
                'success' => true,
                'message' => 'Berhasil menghapus report komentar'
            ]
        );
    }
    public function getCommenterDetails()
    {

    }
    public function upvote()
    {

    }
    public function downVote()
    {

    }
    public function removeVote()
    {

    }
    public function getThread()
    {

    }
    public function getContext()
    {

    }
    public function addReply()
    {

    }
    public function updateComment()
    {

    }
    public function patchComment()
    {

    }
    public function deleteComment()
    {

    }
    public function restoreComment()
    {

    }
}
