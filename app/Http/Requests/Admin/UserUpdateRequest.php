<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_type_id' => 'sometimes',
            'instance_id' => 'required|integer',
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email,'.$this->id,
            'username' => 'required|unique:users,username,'.$this->id,
            'language' => 'required',
            'status' => 'required',
            'can_create_order' => 'required',
            'can_order_detail_edit' => 'required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'  => false,
            'message'  => 'Validation errors',
            'errors'   => $validator->errors()
        ]));
    }
    
}
