<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
class UpdateRequest extends FormRequest
{
    public function rules():array
    {
        return [
       //     'email'     =>'required|string|email|exists:users,email',
          'name'      =>'required|string'
           // 'password'  =>'required|string|min:6'
         ];
    }
}
