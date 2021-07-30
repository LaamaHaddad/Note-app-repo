<?php

namespace App\Http\Requests\Note;

use App\Http\Requests\FormRequest;
class NoteUserIdRequest extends FormRequest
{
    public function rules():array
    {
        return [
            'user_id'=>'required|integer|exists:notes,user_id'
         ];
    }
}
