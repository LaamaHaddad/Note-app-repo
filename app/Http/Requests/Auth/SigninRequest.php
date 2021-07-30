<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
class SigninRequest extends FormRequest
{
    public function rules():array
    {
        return [
            'email'     =>'required|string|email|exists:users,email',
            'password'  =>'required|string|min:6|max:25'
         ];
    }
}
