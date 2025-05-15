<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoadMapRequest extends FormRequest
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
            'roadId'    => 'required|integer',
            'instanceId'=> 'required|integer',
            'stage'     => 'required|integer',
            'userIds'   => "required|exists:users,id"

    //      'userIds' => 'required|array|min:1|max:20', // Kamida 1, ko‘pi bilan 20 element
    //      'userIds.*' => 'required|integer|distinct|exists:users,id', // Takrorlanmasin
        ];
    }
}
