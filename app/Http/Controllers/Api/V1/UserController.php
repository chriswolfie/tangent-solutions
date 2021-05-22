<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPostRequest;
use App\Http\Requests\UserPutRequest;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\UserSneaky as UserSneakyResource;
use App\Repositories\Contracts\Users as UsersContract;
use App\Rules\AutonomousUniqueRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *    title="Tangent Solutions - PHP Assessment",
 *    description="The auto-generated swagger documentation for the Tangent Solutions PHP Assessment.",
 *    version="1.0.0",
 *    @OA\Contact(
 *       name="Chris Kempen",
 *       email="chris@phpalchemist.com"
 *    ),
 * )
 * 
 */

class UserController extends Controller
{
    /**
     *  @OA\Get(
     *      path="/api/v1/user",
     *      summary="Retrieve a list of users",
     *      description="Retrieves a list of all of the users created on the system",
     *      operationId="user-index",
     *      tags={"User"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              example={
     *                  { "user_id": 1, "full_name": "Firstname Lastname", "email": "email@domain.com" },
     *                  { "user_id": 2, "full_name": "Firstname Lastname", "email": "email@domain.com" },
     *              },
     *              @OA\Items(
     *                  @OA\Property(property="user_id", type="integer", example="7"),
     *                  @OA\Property(property="full_name", type="string", example="Firstname Lastname"),
     *                  @OA\Property(property="email", type="string", example="email@domain.com"),
     *              ),
     *          ),
     *      )
     *  )
     */
    public function index(UsersContract $users_contract)
    {
        return UserResource::collection($users_contract->fetchAllEntries());
    }

    /**
     *  @OA\Get(
     *      path="/api/v1/sneaky",
     *      summary="Retrieve a (sneaky) list of users",
     *      description="Retrieves a (sneaky) list of all of the users created on the system, with their API key values",
     *      operationId="user-sneaky",
     *      tags={"Sneaky"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              example={
     *                  { "user_id": 1, "full_name": "Firstname Lastname", "email": "email@domain.com", "api_key": "ABC123" },
     *                  { "user_id": 2, "full_name": "Firstname Lastname", "email": "email@domain.com", "api_key": "DEF456" },
     *              },
     *              @OA\Items(
     *                  @OA\Property(property="user_id", type="integer", example="7"),
     *                  @OA\Property(property="full_name", type="string", example="Firstname Lastname"),
     *                  @OA\Property(property="email", type="string", example="email@domain.com"),
     *                  @OA\Property(property="api_key", type="string", example="ABC123"),
     *              ),
     *          ),
     *      )
     *  )
     */
    public function sneakyAction(UsersContract $users_contract)
    {
        return UserSneakyResource::collection($users_contract->fetchAllEntries());
    }

    /**
     *  @OA\Post(
     *      path="/api/v1/user",
     *      summary="Add a new user",
     *      description="Add a new user to the system",
     *      operationId="user-store",
     *      tags={"User"},
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
    public function store(UserPostRequest $request, UsersContract $users_contract)
    {
        // values have already been validated, so let's now
        // run a custom validation check with the uniqueness rule...
        $rules = $request->rules();
        $rules['email'][] = new AutonomousUniqueRule($users_contract, 'email');
        $validator = Validator::make($request->all(), $rules);
        $validator->validated();

        $user = $users_contract->createUserEntry($request->all());
        if (!$user) {
            return response()->json(['message' => 'User could not be created'], 422);
        }
        return response()->json(new UserResource($user), 201);
    }

    /**
     *  @OA\Get(
     *      path="/api/v1/user/{user_id}",
     *      summary="Retrieve a user",
     *      description="Retrieves a specific user from the system",
     *      operationId="user-show",
     *      tags={"User"},
     *      @OA\Parameter(
     *          name="user_id", in="path", required=true, description="The ID of the user you want to retrieve", @OA\Schema(type="integer", default="1")
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
    public function show($id, UsersContract $users_contract)
    {
        $user = $users_contract->fetchSingleEntry($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return new UserResource($user);
    }

    /**
     *  @OA\Put(
     *      path="/api/v1/user/{user_id}",
     *      summary="Update a user",
     *      description="Update a user on the system, supplying any one of the parameters",
     *      operationId="user-update",
     *      tags={"User"},
     *      @OA\Parameter(
     *          name="user_id", in="path", required=true, description="The ID of the user you want to update", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Updated user particulars",
     *          @OA\JsonContent(
     *              required={},
     *              @OA\Property(property="full_name", type="string", example="Firstname Lastname"),
     *              @OA\Property(property="email", type="string", format="email", example="user@email.com"),
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
    public function update(UserPutRequest $request, $id, UsersContract $users_contract)
    {
        // user exists..?
        $user = $users_contract->fetchSingleEntry($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        // values have already been validated, so let's now
        // run a custom validation check with the uniqueness rule...
        $rules = $request->rules();
        $rules['email'][] = new AutonomousUniqueRule($users_contract, 'email', $id);
        $validator = Validator::make($request->all(), $rules);
        $validator->validated();

        $user = $users_contract->updateEntry($id, $request->all());
        if (!$user) {
            return response()->json(['message' => 'User update failed'], 422);
        }

        return new UserResource($user);
    }

    /**
     *  @OA\Delete(
     *      path="/api/v1/user/{user_id}",
     *      summary="Delete a user",
     *      description="Delete a user from the system",
     *      operationId="user-delete",
     *      tags={"User"},
     *      @OA\Parameter(
     *          name="user_id", in="path", required=true, description="The ID of the user you want to delete", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No content",
     *      ),
     *  )
     */
    public function destroy($id, UsersContract $users_contract)
    {
        $users_contract->removeEntry($id);
        return response()->noContent();
    }
}
