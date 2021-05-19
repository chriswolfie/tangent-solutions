<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserPostRequest extends FormRequest
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
            'full_name' => 'required|min:10',
            'email' => 'required|email|unique:App\Models\Users,email',
        ];
    }

    public static function updateRules()
    {
        return [
            'full_name' => 'nullable|min:10',
            'email' => 'nullable|email|unique:App\Models\Users,email',
        ];
    }
}