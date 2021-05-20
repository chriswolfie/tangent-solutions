<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostPutRequest extends FormRequest
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
            'title' => ['string', 'min:5'],
            'content' => ['string', 'min:10'],
            'user_id' => ['integer'],
            'category_id' => ['integer']
        ];
    }
}
