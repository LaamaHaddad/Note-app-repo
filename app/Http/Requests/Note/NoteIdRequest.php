<?php

namespace App\Http\Requests\Note;

use App\Http\Requests\FormRequest;
class NoteIdRequest extends FormRequest
{
    public function rules():array
    {
        return [
            'id'=>'required|integer|exists:notes,id'
         ];
    }
}
