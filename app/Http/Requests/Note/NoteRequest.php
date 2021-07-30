<?php

namespace App\Http\Requests\Note;

use App\Http\Requests\FormRequest;
class NoteRequest extends FormRequest
{
    public function rules():array
    {
        return [
            'title'=>'required|string',
            'description'=>'required|string',
         ];
    }
}
