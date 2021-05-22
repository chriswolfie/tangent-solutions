<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryPostRequest;
use App\Http\Resources\Category as CategoryResource;
use App\Repositories\Contracts\Categories as CategoriesContract;
use App\Rules\AutonomousUniqueRule;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     *  @OA\Get(
     *      path="/api/v1/category",
     *      summary="Retrieve a list of categories",
     *      description="Retrieves a list of all of the categories created on the system",
     *      operationId="category-index",
     *      tags={"Category"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              example={
     *                  { "category_id": 1, "label": "First Category" },
     *                  { "category_id": 2, "label": "Second Category" },
     *              },
     *              @OA\Items(
     *                  @OA\Property(property="category_id", type="integer", example="7"),
     *                  @OA\Property(property="label", type="string", example="First Category"),
     *              ),
     *          ),
     *      )
     *  )
     */
    public function index(CategoriesContract $categories_contract)
    {
        return CategoryResource::collection($categories_contract->fetchAllEntries());
    }

    /**
     *  @OA\Post(
     *      path="/api/v1/category",
     *      summary="Add a new category",
     *      description="Add a new category to the system",
     *      operationId="category-store",
     *      tags={"Category"},
     *      @OA\RequestBody(
     *          required=true,
     *          description="New category particulars",
     *          @OA\JsonContent(
     *              required={"label"},
     *              @OA\Property(property="label", type="label", example="Category Label"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Category created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="category_id", type="integer", example="7"),
     *              @OA\Property(property="label", type="string", example="First Category"),
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
    public function store(CategoryPostRequest $request, CategoriesContract $categories_contract)
    {
        // values have already been validated, so let's now
        // run a custom validation check with the uniqueness rule...
        $rules = $request->rules();
        $rules['label'][] = new AutonomousUniqueRule($categories_contract, 'label');
        $validator = Validator::make($request->all(), $rules);
        $validator->validated();
        
        $category = $categories_contract->createEntry($request->all());
        if (!$category) {
            return response()->json(['message' => 'Category could not be created'], 422);
        }
        return response()->json(new CategoryResource($category), 201);
    }

    /**
     *  @OA\Get(
     *      path="/api/v1/category/{category_id}",
     *      summary="Retrieves a specific category",
     *      description="Retrieves a specific category from the available categories on the system",
     *      operationId="category-show",
     *      tags={"Category"},
     *      @OA\Parameter(
     *          name="category_id", in="path", required=true, description="The ID of the category you want to retrieve", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="category_id", type="integer", example="7"),
     *              @OA\Property(property="label", type="string", example="First Category"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Category not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Category not found."),
     *          )
     *      )
     *  )
     */
    public function show($id, CategoriesContract $categories_contract)
    {
        $category = $categories_contract->fetchSingleEntry($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return new CategoryResource($category);
    }

    /**
     *  @OA\Put(
     *      path="/api/v1/category/{category_id}",
     *      summary="Update a category",
     *      description="Update a category on the system, supplying any one of the parameters",
     *      operationId="category-update",
     *      tags={"Category"},
     *      @OA\Parameter(
     *          name="category_id", in="path", required=true, description="The ID of the category you want to update", @OA\Schema(type="integer", default="1")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Updated category particulars",
     *          @OA\JsonContent(
     *              required={"label"},
     *              @OA\Property(property="label", type="string", example="Some New Label"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Category updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="category_id", type="integer", example="7"),
     *              @OA\Property(property="label", type="string", example="First Category"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Category not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Category not found."),
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
    public function update(CategoryPostRequest $request, $id, CategoriesContract $categories_contract)
    {
        // checking for existence...
        $category = $categories_contract->fetchSingleEntry($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // values have already been validated, so let's now
        // run a custom validation check with the uniqueness rule...
        $rules = $request->rules();
        $rules['label'][] = new AutonomousUniqueRule($categories_contract, 'label', $id);
        $validator = Validator::make($request->all(), $rules);
        $validator->validated();

        $category = $categories_contract->updateEntry($id, $request->all());
        if (!$category) {
            return response()->json(['message' => 'Category update failed'], 422);
        }
        
        return new CategoryResource($category);
    }

    /**
     *  @OA\Delete(
     *      path="/api/v1/category/{category_id}",
     *      summary="Delete a category",
     *      description="Delete a category from the system",
     *      operationId="category-delete",
     *      tags={"Category"},
     *      @OA\Parameter(
     *          name="category_id", in="path", required=true, description="The ID of the category you want to delete", @OA\Schema(type="integer", default="1")
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
    public function destroy($id, CategoriesContract $categories_contract)
    {
        $categories_contract->removeCategory($id);
        return response()->noContent();
    }
}
