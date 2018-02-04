<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUser extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
//            'first_name' => 'required|string|max:50',
//            'last_name' => 'required|string|max:50',
//            'username' => 'required|string|alpha_dash|max:60|unique:users',
//            'email' => 'required|string|email|max:190|unique:users',
//            'password' => 'required|string|min:6|max:30|confirmed',
        ];
    }

}
