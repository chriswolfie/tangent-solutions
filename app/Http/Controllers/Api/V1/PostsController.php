<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostPostRequest;
use App\Http\Resources\Post as PostResource;
use App\Repositories\Contracts\Posts as PostsContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostsController extends Controller
{
    /**
     *  @OA\Get(
     *      path="/api/v1/post",
     *      summary="Retrieve a list of post",
     *      description="Retrieves a list of all of the posts created on the system",
     *      operationId="post-index",
     *      tags={"Post"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  example={
     *                      { "user_id": 1, "full_name": "Firstname Lastname", "email_address": "email@domain.com" },
     *                      { "user_id": 2, "full_name": "Firstname Lastname", "email_address": "email@domain.com" },
     *                  },
     *                  @OA\Items(
     *                      @OA\Property(property="user_id", type="integer", example="7"),
     *                      @OA\Property(property="full_name", type="string", example="Firstname Lastname"),
     *                      @OA\Property(property="email_address", type="string", example="email@domain.com"),
     *                  ),
     *              ),
     *          ),
     *      )
     *  )
     */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PostsContract $posts_contract)
    {
        return PostResource::collection($posts_contract->fetchAllEntries());
    }

    /**
     *  @OA\Post(
     *      path="/api/v1/post",
     *      summary="Add a new post",
     *      description="Add a new post to the system",
     *      operationId="post-store",
     *      tags={"Post"},
     *      @OA\RequestBody(
     *          required=true,
     *          description="New post particulars",
     *          @OA\JsonContent(
     *              required={"title","content","user_id","category_id"},
     *              @OA\Property(property="title", type="string", example="Some Title"),
     *              @OA\Property(property="content", type="string", example="This is an example of the content."),
     *              @OA\Property(property="user_id", type="integer", example="3"),
     *              @OA\Property(property="category_id", type="integer", example="2"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Post created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="user_id", type="integer", example="12"),
     *              @OA\Property(property="full_name", type="string", example="Firstname Lastname"),
     *              @OA\Property(property="email", type="string", example="email@address.com"),
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
     *                      "full_name" : {"Invalid full name."},
     *                      "email" : {"The email has already been taken."},
     *                  },
     *                  @OA\Items(
     *                      @OA\Property(property="full_name", type="array", example={"Invalid full name."}, @OA\Items()),
     *                      @OA\Property(property="email", type="array", example={"The email has already been taken."}, @OA\Items()),
     *                  ),
     *              ),
     *          )
     *      )
     *  )
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostPostRequest $request, PostsContract $posts_contract)
    {
        $_ = $request->validated();
        $post = $posts_contract->createEntry($request->all());
        if (!$post) {
            return response()->json(['message' => 'Post could not be created'], 422);
        }
        return response()->json(new PostResource($post), 201);
    }

    /**
     *  @OA\Get(
     *      path="/api/v1/post/{post_id}",
     *      summary="Retrieves a specific post",
     *      description="Retrieves a specific post from the available posts on the system",
     *      operationId="post-show",
     *      tags={"Post"},
     *      @OA\Parameter(
     *          name="post_id", in="path", required=true, description="The ID of the post you want to retrieve", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="user_id", type="integer", example="12"),
     *              @OA\Property(property="full_name", type="string", example="Firstname Lastname"),
     *              @OA\Property(property="email", type="string", example="email@address.com"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found."),
     *          )
     *      )
     *  )
     */
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, PostsContract $posts_contract)
    {
        $post = $posts_contract->fetchSingleEntry($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        return new PostResource($post);
    }

    /**
     *  @OA\Put(
     *      path="/api/v1/post/{post_id}",
     *      summary="Update a post",
     *      description="Update a post on the system, supplying any one of the parameters",
     *      operationId="post-update",
     *      tags={"Post"},
     *      @OA\Parameter(
     *          name="post_id", in="path", required=true, description="The ID of the post you want to update", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Updated post particulars",
     *          @OA\JsonContent(
     *              required={},
     *              @OA\Property(property="title", type="string", example="Some Title"),
     *              @OA\Property(property="content", type="string", example="This is an example of the content."),
     *              @OA\Property(property="user_id", type="integer", example="3"),
     *              @OA\Property(property="category_id", type="integer", example="2"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="user_id", type="integer", example="12"),
     *              @OA\Property(property="full_name", type="string", example="Firstname Lastname"),
     *              @OA\Property(property="email", type="string", example="email@address.com"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found."),
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
     *                      "full_name" : {"Invalid full name."},
     *                      "email" : {"The email has already been taken."},
     *                  },
     *                  @OA\Items(
     *                      @OA\Property(property="full_name", type="array", example={"Invalid full name."}, @OA\Items()),
     *                      @OA\Property(property="email", type="array", example={"The email has already been taken."}, @OA\Items()),
     *                  ),
     *              ),
     *          )
     *      )
     *  )
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, PostsContract $posts_contract)
    {
        $rules = PostPostRequest::updateRules();
        $rules['title'] .= ',' . $id . ',id';
        $validator = Validator::make($request->all(), $rules);
        $_ = $validator->validated();

        $post = $posts_contract->updateEntry($id, $request->all());
        if (!$post) {
            return response()->json(['message' => 'Post update failed'], 422);
        }
        
        return new PostResource($post);
    }

    /**
     *  @OA\Delete(
     *      path="/api/v1/post/{post_id}",
     *      summary="Delete a post",
     *      description="Delete a post from the system",
     *      operationId="post-delete",
     *      tags={"Post"},
     *      @OA\Parameter(
     *          name="post_id", in="path", required=true, description="The ID of the post you want to delete", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No content",
     *      ),
     *  )
     */
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, PostsContract $posts_contract)
    {
        $posts_contract->removeEntry($id);
        return response()->noContent();
    }
}
