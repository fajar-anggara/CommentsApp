<?php

namespace App\Http\Controllers;

use App\Enums\LogEvents;
use App\Exceptions\CommentNotFoundException;
use App\Facades\Comment as CommentDo;
use App\Facades\Fractal;
use App\Facades\Like;
use App\Facades\SetLog;
use App\Facades\Voting;
use App\Http\Requests\CommentAddRepotRequest;
use App\Repositories\Interfaces\CommenterRepository;
use Illuminate\Http\JsonResponse;

class Comment
{
    public function __construct(
        protected CommenterRepository $commenterRepository,
    ){}
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
        SetLog::withEvent(LogEvents::PREPARE)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'getReplies'
                ]
            ])
            ->withMessage('Prepare to get replies')
            ->build();

        $comments = CommentDo::getReplies($commentId);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memuat balasan komentar',
            'data' => Fractal::useCommentTransformer($comments)->buildWithArraySerializer()
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
            ->withMessage('Prepare to add like')
            ->build();

        Like::add($commentId, auth()->guard()->user()->id);

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
    public function deleteLike(string $commentId): JsonResponse
    {
        SetLog::withEvent(LogEvents::PREPARE)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'deleteLike'
                ]
            ])
            ->withMessage('Prepare to delete like')
            ->build();

        Like::remove($commentId, auth()->guard()->user()->id);

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
    public function addReport(string $commentId, CommentAddRepotRequest $request): JsonResponse
    {
        SetLog::withEvent(LogEvents::PREPARE)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'addReport'
                ]
            ])
            ->withMessage('Prepare to add report')
            ->build();

        CommentDo::addReport(
            $commentId,
            auth()->guard()->user()->id,
            $request->validated()['reason']
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
    public function deleteReport(string $commentId): JsonResponse
    {
        SetLog::withEvent(LogEvents::PREPARE)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'deleteReport'
                ]
            ])
            ->withMessage('Prepare to delete report')
            ->build();

        CommentDo::deleteReport(
            $commentId,
            auth()->guard()->user()->id
        );

        return response()->json(
            [
                'success' => true,
                'message' => 'Berhasil menghapus report komentar'
            ]
        );
    }


    /**
     * @OA\Get(
     *     path="/commenters/{commenterId}",
     *     summary="Get commenter details",
     *     description="Get commenter details",
     *     operationId="getCommenterDetails",
     *     tags={"Commenters"},
     *     @OA\Parameter(
     *         description="Commenter's ID",
     *         in="path",
     *         name="commenterId",
     *         required=true,
     *         example="1",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success to get commenter details",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/CommenterTransformer"),
     *              @OA\Property(property="message", type="string", example="Berhasil mengambil detail commenter")
     *          )
     *      ),
     *     @OA\Response(
     *          response=404,
     *          description="Commenter tidak ditemukan",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Commenter tidak ditemukan")
     *          )
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="Gagal mengambil detail commenter. Harap coba lagi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Gagal mengambil detail commenter. Harap coba lagi")
     *          )
     *      ),
     * )
     */
    public function getCommenterDetails(string $commenterId): JsonResponse
    {
        SetLog::withEvent(LogEvents::PREPARE)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'getCommenterDetails'
                ]
            ])
            ->withMessage('Prepare to get commenter details')
            ->build();

        $commenter = $this->commenterRepository->findById($commenterId);
        if (!$commenter) {
            throw new CommentNotFoundException(
                "User tidak ditemukan",
                [
                    'commenter_id' => $commenterId
                ]
            );
        }

        SetLog::withEvent(LogEvents::FETCHING_COMMENTER)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'getCommenterDetails'
                ]
            ])
            ->withMessage('Fetching commenter')
            ->build();

        return response()->json(
            [
                'success' => true,
                'message' => 'Berhasil mengambil detail commenter',
                'data' => Fractal::useCommenterTransformer($commenter)
                    ->withIncludes([
                        'badges',
                        'details',
                        'statistics'
                    ])
                    ->buildWithArraySerializer()
            ]
        );
    }
    public function upvote(string $commentId): JsonResponse
    {
        SetLog::withEvent(LogEvents::PREPARE)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'upvote'
                ]
            ])
            ->withMessage('Prepare to upvote')
            ->build();

        Voting::upvote(
            $commentId,
            auth()->guard()->user()->id
        );

        return response()->json(
            [
                'success' => true,
                'message' => 'Berhasil upvote'
            ]);
    }

    public function downvote(string $commentId): JsonResponse
    {
        SetLog::withEvent(LogEvents::FETCHING)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'downvote'
                ]
            ])
            ->withMessage('Prepare to downvote')
            ->build();

        Voting::downvote(
            $commentId,
            auth()->guard()->user()->id
        );

        return response()->json(
            [
                'success' => true,
                'message' => 'Berhasil downvote'
            ]);
    }

    public function removeVote(string $commentId, string $upOrDownVote): JsonResponse
    {
        SetLog::withEvent(LogEvents::DELETE)
            ->withProperties([
                'performedOn' => [
                    'class' => Comment::class,
                    'method' => 'removeVote'
                ]
            ])
            ->withMessage('Prepare to remove vote')
            ->build();

        Voting::remove(
            $commentId,
            auth()->guard()->user()->id,
            $upOrDownVote
        );

        return response()->json(
            [
                'success' => true,
                'message' => 'Berhasil remove vote'
            ]);
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
