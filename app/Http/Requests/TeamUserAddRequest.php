<?php

namespace App\Http\Requests;

use App\Helper\ErrorHelperResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class TeamUserAddRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function messages()
    {
        return [
            'team_id.required' => 'The team id field is required',
            'user_id.required' => 'The user id field is required',
        ];
    }
    public function rules()
    {
        return [
            'team_id'=>'required',
            'user_id'=>'required',
        ];
    }
}
