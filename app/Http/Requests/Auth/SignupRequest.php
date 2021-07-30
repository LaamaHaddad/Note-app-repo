<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
class SignupRequest extends FormRequest
{
    public function rules():array
    {
        return [
            'name'      =>'required|string|max:25|min:6',
            'email'     =>'required|email|string|unique:users,email',
            'password'  =>'required|string|min:6|max:25',
         ];

    }
}
