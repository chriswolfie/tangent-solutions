<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SuperSimpleAuthenticator;
use App\Http\Requests\CommentPostRequest;
use App\Http\Resources\Comment as CommentResource;
use App\Repositories\Contracts\Comments as CommentsContract;
use App\Repositories\Contracts\Users as UsersContract;
use App\Rules\AutonomousExists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware(SuperSimpleAuthenticator::class)->only(['store', 'update', 'destroy']);
    }

    /**
     *  @OA\Get(
     *      path="/api/v1/post/{post_id}/comment",
     *      summary="Retrieve a list of post comments",
     *      description="Retrieves a list of all of the post comments created on the system for a given post",
     *      operationId="comment-index",
     *      tags={"Comment"},
     *      @OA\Parameter(
     *          name="post_id", in="path", required=true, description="The ID of the post you want to retrieve comments for", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="comment_id", type="integer", example="1"),
     *              @OA\Property(property="content", type="string", example="The comment content."),
     *              @OA\Property(
     *                  property="user",
     *                  type="array",
     *                  example={
     *                      "user_id" : "2",
     *                      "full_name" : "Full Name",
     *                      "email" : "email@domain.com",
     *                  },
     *                  @OA\Items(
     *                      @OA\Property(property="user_id", type="integer", example="2", @OA\Items()),
     *                      @OA\Property(property="full_name", type="string", example="Full Name", @OA\Items()),
     *                      @OA\Property(property="email", type="string", example="email@domain.com", @OA\Items()),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Post not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Post not found."),
     *          )
     *      )
     *  )
     */
    public function index(int $post, CommentsContract $comments_contract)
    {
        $comments = $comments_contract->fetchAllCommentsFromPost($post);
        return CommentResource::collection($comments);
    }

    /**
     *  @OA\Post(
     *      path="/api/v1/post/{post_id}/comment",
     *      summary="Add a new post comment",
     *      description="Add a new comment to the specified post on the system",
     *      operationId="comment-store",
     *      tags={"Comment"},
     *      security={ {"api_token": {} } },
     *      @OA\Parameter(
     *          name="post_id", in="path", required=true, description="The ID of the post you want to retrieve comments for", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="New post particulars",
     *          @OA\JsonContent(
     *              required={"content"},
     *              @OA\Property(property="content", type="string", example="This is an example of the content."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Post created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="comment_id", type="integer", example="1"),
     *              @OA\Property(property="content", type="string", example="The comment content."),
     *              @OA\Property(
     *                  property="user",
     *                  type="array",
     *                  example={
     *                      "user_id" : 2,
     *                      "full_name" : "Full Name",
     *                      "email" : "email@domain.com",
     *                  },
     *                  @OA\Items(
     *                      @OA\Property(property="user_id", type="integer", example="2", @OA\Items()),
     *                      @OA\Property(property="full_name", type="string", example="Full Name", @OA\Items()),
     *                      @OA\Property(property="email", type="string", example="email@domain.com", @OA\Items()),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorised",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorised"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Post not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Post not found."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *                  example={
     *                      "parameter" : {"Some error message."},
     *                      "another_parameter" : {"Another error message."},
     *                  },
     *                  @OA\Items(
     *                      @OA\Property(property="parameter", type="array", example={"Another error message."}, @OA\Items()),
     *                      @OA\Property(property="another_parameter", type="array", example={"Some error message."}, @OA\Items()),
     *                  ),
     *              ),
     *          )
     *      )
     *  )
     */
    public function store(CommentPostRequest $request, $post, CommentsContract $comments_contract, UsersContract $users_contract)
    {
        // values have already been validated, so let's now
        // run a custom validation check with the uniqueness rule...
        $rules = $request->rules();
        $rules['user_id'][] = new AutonomousExists($users_contract, 'id');
        $validator = Validator::make($request->all(), $rules);
        $validator->validated();

        $comment = $comments_contract->createComment($post, $request->input('user_id'), $request->input('content'));
        if (!$comment) {
            return response()->json(['message' => 'Comment could not be created'], 422);
        }
        return response()->json(new CommentResource($comment), 201);
    }

    /**
     *  @OA\Get(
     *      path="/api/v1/post/{post_id}/comment/{comment_id}",
     *      summary="Retrieves a specific post comment",
     *      description="Retrieves a specific post comment from the specified post on the system",
     *      operationId="comment-show",
     *      tags={"Comment"},
     *      @OA\Parameter(
     *          name="post_id", in="path", required=true, description="The ID of the post you want to retrieve", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\Parameter(
     *          name="comment_id", in="path", required=true, description="The ID of the comment you want to retrieve", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="comment_id", type="integer", example="1"),
     *              @OA\Property(property="content", type="string", example="The comment content."),
     *              @OA\Property(
     *                  property="user",
     *                  type="array",
     *                  example={
     *                      "user_id" : 2,
     *                      "full_name" : "Full Name",
     *                      "email" : "email@domain.com",
     *                  },
     *                  @OA\Items(
     *                      @OA\Property(property="user_id", type="integer", example="2", @OA\Items()),
     *                      @OA\Property(property="full_name", type="string", example="Full Name", @OA\Items()),
     *                      @OA\Property(property="email", type="string", example="email@domain.com", @OA\Items()),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Comment not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Comment not found."),
     *          )
     *      )
     *  )
     */
    public function show($post, $id, CommentsContract $comments_contract)
    {
        $comment = $comments_contract->fetchSingleEntry($id);
        if (!$comment || $comment->post_id != $post) {
            return response()->json(['message' => 'Comment could not be found'], 404);
        }
        return new CommentResource($comment);
    }

    /**
     *  @OA\Put(
     *      path="/api/v1/post/{post_id}/comment/{comment_id}",
     *      summary="Update a comment",
     *      description="Update a comment on the system, given the comment and post id, supplying any one of the parameters",
     *      operationId="comment-update",
     *      tags={"Comment"},
     *      security={ {"api_token": {} } },
     *      @OA\Parameter(
     *          name="post_id", in="path", required=true, description="The ID of the post you want to update", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\Parameter(
     *          name="comment_id", in="path", required=true, description="The ID of the comment you want to retrieve", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Updated comment particulars",
     *          @OA\JsonContent(
     *              required={},
     *              @OA\Property(property="content", type="string", example="This is an example of the new content."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Comment updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="comment_id", type="integer", example="1"),
     *              @OA\Property(property="content", type="string", example="The comment content."),
     *              @OA\Property(
     *                  property="user",
     *                  type="array",
     *                  example={
     *                      "user_id" : 2,
     *                      "full_name" : "Full Name",
     *                      "email" : "email@domain.com",
     *                  },
     *                  @OA\Items(
     *                      @OA\Property(property="user_id", type="integer", example="2", @OA\Items()),
     *                      @OA\Property(property="full_name", type="string", example="Full Name", @OA\Items()),
     *                      @OA\Property(property="email", type="string", example="email@domain.com", @OA\Items()),
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorised",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorised"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Comment not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Comment not found."),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *                  example={
     *                      "parameter" : {"Some error message."},
     *                      "another_parameter" : {"Another error message."},
     *                  },
     *                  @OA\Items(
     *                      @OA\Property(property="parameter", type="array", example={"Another error message."}, @OA\Items()),
     *                      @OA\Property(property="another_parameter", type="array", example={"Some error message."}, @OA\Items()),
     *                  ),
     *              ),
     *          )
     *      )
     *  )
     */
    public function update(CommentPostRequest $request, $post, $id, CommentsContract $comments_contract)
    {
        // first, let's check that these are in fact linked...
        $comment = $comments_contract->fetchSingleEntry($id);
        if (!$comment || $comment->post_id != $post) {
            return response()->json(['message' => 'Comment could not be found'], 404);
        }

        // moving on to the update...
        $comment = $comments_contract->updateComment($id, $request->input('content', ''));
        if (!$comment) {
            return response()->json(['message' => 'Comment could not be created'], 422);
        }
        return new CommentResource($comment);
    }

    /**
     *  @OA\Delete(
     *      path="/api/v1/post/{post_id}/comment/{comment_id}",
     *      summary="Delete a comment",
     *      description="Delete a comment from the system, given a particular post",
     *      operationId="comment-delete",
     *      tags={"Comment"},
     *      security={ {"api_token": {} } },
     *      @OA\Parameter(
     *          name="post_id", in="path", required=true, description="The ID of the post you want to delete", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\Parameter(
     *          name="comment_id", in="path", required=true, description="The ID of the comment you want to retrieve", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No content",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorised",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorised"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Comment not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Comment not found."),
     *          )
     *      ),
     *  )
     */
    public function destroy($post, $id, CommentsContract $comments_contract)
    {
        // first, let's check that these are in fact linked...
        $comment = $comments_contract->fetchSingleEntry($id);
        if (!$comment || $comment->post_id != $post) {
            return response()->json(['message' => 'Comment could not be found'], 404);
        }

        $comments_contract->removeEntry($id);
        return response()->noContent();
    }
}
