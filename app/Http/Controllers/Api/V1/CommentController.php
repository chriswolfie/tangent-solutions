<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\Comments as CommentsContract;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     *  @OA\Get(
     *      path="/api/v1/post/{post_id}/comment",
     *      summary="Retrieve a list of comments",
     *      description="Retrieves a list of all comments for a particular post",
     *      operationId="comment-index",
     *      tags={"Comment"},
     *      @OA\Parameter(
     *          name="post_id", in="path", required=true, description="The ID of the post you'd like to retrieve comments for", @OA\Schema(type="integer", default="1")
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
