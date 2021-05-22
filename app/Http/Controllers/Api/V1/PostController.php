<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SuperSimpleAuthenticator;
use App\Http\Requests\PostPostRequest;
use App\Http\Requests\PostPutRequest;
use App\Http\Resources\Post as PostResource;
use App\Repositories\Contracts\Categories as CategoriesContract;
use App\Repositories\Contracts\Posts as PostsContract;
use App\Repositories\Contracts\Users as UsersContract;
use App\Rules\AutonomousExists;
use App\Rules\AutonomousUniqueRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware(SuperSimpleAuthenticator::class)->only(['store', 'update', 'destroy']);
    }
    
    /**
     *  @OA\Get(
     *      path="/api/v1/post",
     *      summary="Retrieve a list of posts",
     *      description="Retrieves a list of all of the posts created on the system",
     *      operationId="post-index",
     *      tags={"Post"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              example={
     *                  { "post_id": 1, "post_title": "Title Of Post", "post_content": "Some post content.", "user_id": 1, "category_id": 1 },
     *                  { "post_id": 2, "post_title": "Another Title", "post_content": "Some post content.", "user_id": 1, "category_id": 3 },
     *              },
     *              @OA\Items(
     *                  @OA\Property(property="post_id", type="integer", example="1"),
     *                  @OA\Property(property="post_title", type="string", example="Title Of Post"),
     *                  @OA\Property(property="post_content", type="string", example="Some post content."),
     *                  @OA\Property(property="user_id", type="integer", example="7"),
     *                  @OA\Property(property="category_id", type="integer", example="2"),
     *              ),
     *          ),
     *      )
     *  )
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
     *      security={ {"api_token": {} } },
     *      @OA\RequestBody(
     *          required=true,
     *          description="New post particulars",
     *          @OA\JsonContent(
     *              required={"title","content","category_id"},
     *              @OA\Property(property="title", type="string", example="Some Title"),
     *              @OA\Property(property="content", type="string", example="This is an example of the content."),
     *              @OA\Property(property="category_id", type="integer", example="2"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Post created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="post_id", type="integer", example="1"),
     *              @OA\Property(property="post_title", type="string", example="Title Of Post"),
     *              @OA\Property(property="post_content", type="string", example="Some post content."),
     *              @OA\Property(property="user_id", type="integer", example="7"),
     *              @OA\Property(property="category_id", type="integer", example="2"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorised",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorised"),
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
    public function store(PostPostRequest $request, PostsContract $posts_contract, UsersContract $users_contract, CategoriesContract $categories_contract)
    {
        // values have already been validated, so let's now
        // run a custom validation check with the uniqueness rule...
        $rules = $request->rules();
        $rules['title'][] = new AutonomousUniqueRule($posts_contract, 'title');
        $rules['user_id'][] = new AutonomousExists($users_contract, 'id');
        $rules['category_id'][] = new AutonomousExists($categories_contract, 'id');
        $validator = Validator::make($request->all(), $rules);
        $validator->validated();

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
     *              @OA\Property(property="post_id", type="integer", example="1"),
     *              @OA\Property(property="post_title", type="string", example="Title Of Post"),
     *              @OA\Property(property="post_content", type="string", example="Some post content."),
     *              @OA\Property(property="user_id", type="integer", example="7"),
     *              @OA\Property(property="category_id", type="integer", example="2"),
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
     *      security={ {"api_token": {} } },
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
     *              @OA\Property(property="category_id", type="integer", example="2"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Post updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="post_id", type="integer", example="1"),
     *              @OA\Property(property="post_title", type="string", example="Title Of Post"),
     *              @OA\Property(property="post_content", type="string", example="Some post content."),
     *              @OA\Property(property="user_id", type="integer", example="7"),
     *              @OA\Property(property="category_id", type="integer", example="2"),
     *          )
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
    public function update(PostPutRequest $request, $id, PostsContract $posts_contract, UsersContract $users_contract, CategoriesContract $categories_contract)
    {
        // check post exists...
        $post = $posts_contract->fetchSingleEntry($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        // values have already been validated, so let's now
        // run a custom validation check with the uniqueness rule...
        $rules = $request->rules();
        $rules['title'][] = new AutonomousUniqueRule($posts_contract, 'title', $id);
        $rules['user_id'][] = new AutonomousExists($users_contract, 'id');
        $rules['category_id'][] = new AutonomousExists($categories_contract, 'id');
        $validator = Validator::make($request->all(), $rules);
        $validator->validated();

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
     *      security={ {"api_token": {} } },
     *      @OA\Parameter(
     *          name="post_id", in="path", required=true, description="The ID of the post you want to delete", @OA\Schema(type="integer", default="1")
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
     *  )
     */
    public function destroy($id, PostsContract $posts_contract)
    {
        $posts_contract->removeEntry($id);
        return response()->noContent();
    }
}
