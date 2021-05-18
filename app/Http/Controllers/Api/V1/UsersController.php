<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Users;
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

class UsersController extends Controller
{
    /**
     *  @OA\Get(
     *      path="/api/v1/users",
     *      summary="Retrieve a list of users",
     *      description="Retrieves a list of all of the users created on the system",
     *      operationId="users-index",
     *      tags={"users"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="current_page", type="integer", example="2"),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  example={
     *                      { "full_name": "Firstname Lastname", "email_address": "email@domain.com" },
     *                      { "full_name": "Firstname Lastname", "email_address": "email@domain.com" },
     *                  },
     *                  @OA\Items(
     *                      @OA\Property(property="full_name", type="string", example="Firstname Lastname"),
     *                      @OA\Property(property="email_address", type="string", example="email@domain.com"),
     *                  ),
     *              ),
     *              @OA\Property(property="first_page_url", type="string", example="http://domain.com/api/v1/users?page=1"),
     *              @OA\Property(property="from", type="integer", example="2"),
     *              @OA\Property(property="last_page", type="integer", example="5"),
     *              @OA\Property(property="last_page_url", type="string", example="http://domain.com/api/v1/users?page=9"),
     *              @OA\Property(
     *                  property="links",
     *                  type="array",
     *                  example={
     *                      { "url": null, "label": "Previous", "active": false },
     *                      { "url": "http://domain.com/api/v1/users?page=1", "label": "1", "active": true },
     *                  },
     *                  @OA\Items(
     *                      @OA\Property(property="url", type="string", example="http://domain.com/api/v1/users?page=1"),
     *                      @OA\Property(property="label", type="string", example="Previous"),
     *                      @OA\Property(property="active", type="boolean", example="true"),
     *                  ),
     *              ),
     *              @OA\Property(property="next_page_url", type="string", example="http://domain.com/api/v1/users?page=4"),
     *              @OA\Property(property="path", type="string", example="http://domain.com/api/v1/users"),
     *              @OA\Property(property="per_page", type="integer", example="7"),
     *              @OA\Property(property="prev_page_url", type="string", example="http://domain.com/api/v1/users?page=1"),
     *              @OA\Property(property="to", type="integer", example="5"),
     *              @OA\Property(property="total", type="integer", example="15"),
     *          ),
     *      )
     *  )
     */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Users::paginate(5);
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
