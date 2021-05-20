<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|unique:posts,title',
            'content' => 'required|string|min:10',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id'
        ];
    }

    public static function updateRules()
    {
        return [
            'title' => 'nullable|unique:posts,title',
            'content' => 'nullable|string|min:10',
            'user_id' => 'nullable|exists:users,id',
            'category_id' => 'nullable|exists:categories,id'
        ];
    }
}
