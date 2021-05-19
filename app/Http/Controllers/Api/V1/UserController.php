<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPostRequest;
use App\Http\Resources\User as UserResource;
use App\Models\Users;
use App\Repositories\Contracts\Users as UsersContract;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *    title="Tangent Solutions - PHP Assessment",
 *    description="The auto-generated swagger documentation for the Tangent Solutions PHP Assessment.",
 *    version="1.0.0",
 *    @OA\Contact(
 *       name="Chris Kempen",
 *       email="chris@phpalchemist.com"
 *    )
 * )
 */

class UserController extends Controller
{
    /**
     *  @OA\Get(
     *      path="/api/v1/user",
     *      summary="Retrieve a list of users",
     *      description="Retrieves a list of all of the users created on the system",
     *      operationId="user-index",
     *      tags={"user"},
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
    public function index(UsersContract $users_contract)
    {
        return UserResource::collection($users_contract->allUsers());
    }

    /**
     *  @OA\Post(
     *      path="/api/v1/user",
     *      summary="Add a new user",
     *      description="Add a new user to the system",
     *      operationId="user-store",
     *      tags={"user"},
     *      @OA\RequestBody(
     *          required=true,
     *          description="New user particulars",
     *          @OA\JsonContent(
     *              required={"full_name","email"},
     *              @OA\Property(property="full_name", type="string", example="Firstname Lastname"),
     *              @OA\Property(property="email", type="string", format="email", example="user@email.com"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User created successfully",
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
    public function store(UserPostRequest $request, UsersContract $users_contract)
    {
        $_ = $request->validated();
        $user = $users_contract->createUser($request->input('full_name', ''), $request->input('email', ''));
        return response()->json(new UserResource($user), 201);
    }

    /**
     *  @OA\Get(
     *      path="/api/v1/user/{user_id}",
     *      summary="Retrieve a list of users",
     *      description="Retrieves a list of all of the users created on the system",
     *      operationId="user-show",
     *      tags={"user"},
     *      @OA\Parameter(
     *          name="user_id", in="path", required=true, description="The ID of the user you want to retrieve", @OA\Schema(type="integer", default="1")
     *      ),
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return [$id];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
