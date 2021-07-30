<?php

namespace App\Http\Requests\Note;

use App\Http\Requests\FormRequest;
class NoteUpdateRequest extends FormRequest
{
    public function rules():array
    {
        return [
            'title'=>'required',
            'description'=>'required',
            'id'=>'required|integer|exists:notes,id'
         ];
    }
}
