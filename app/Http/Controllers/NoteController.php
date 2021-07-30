<?php

namespace App\Http\Controllers;
use App\Http\Requests\Note\NoteIdRequest;
use App\Http\Requests\Note\NoteRequest;
use App\Http\Requests\Note\NoteUpdateRequest;
use App\Http\Requests\Note\NoteUserIdRequest;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Note as NoteResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\Environment\Console;
use Illuminate\Support\Str;
class NoteController extends Controller
{

    public function receiveImage(Request $request)
    {
        $base64_image = $request->input('imgBase64'); // your base64 encoded
        @list($type, $file_data) = explode(';', $base64_image);
        @list(, $file_data) = explode(',', $file_data);
        $imageName = 'lolo'.'.'.'png';
        Storage::disk('local')->put($imageName, base64_decode($file_data));
      //  $path = public_path() . 'uploads/';
      //  $file->move($path, $file->getClientOriginalName());
        return $imageName;

//         define('UPLOAD_DIR', '/');
//return $img;
//$img = str_replace('data:image/png;base64,', '', $img);
//$img = str_replace(' ', '+', $img);
//$data = base64_decode($img);
//return $img;
// $file = UPLOAD_DIR . uniqid() . '.png';

// $success = file_put_contents($file, $data);
// print $success ? $file : 'Unable to save the file.';


    }
    //--------------------------
    public function index()
    {
        $notes=Note::all();
        return $this->response('success',NoteResource::collection($notes));
    }
    public function notesUser(NoteUserIdRequest $request)
    {
       $notes=Note::where('user_id',$request->user_id)->get();
       if(is_null($notes)){
        return $this->response('exist_notes_f');
      }
      return $this->response('exist_notes_t',NoteResource::collection($notes));
    }
    public function myNotes()
    {
       $notes=Note::where('user_id',Auth::user()->id)->get();
       if(is_null($notes)){
        return $this->response('exist_notes_f');
      }
      return $this->response('exist_notes_t',NoteResource::collection($notes));
    }

    public function filteredMyNotes(Request $request)
    {
        //    $notes=Note::where('user_id',Auth::user()->id)->where('title','=',$request->keyword)->get();
        $notes=Note::where('user_id','=',Auth::user()->id)
                   ->where(function ($query) use ($request) {
                    $query->where('title','LIKE',"%".$request->keyword."%");
                    $query->orWhere('description','LIKE',"%".$request->keyword."%");
                })
                   ->get();
        if(is_null($notes)){
            return $this->response('exist_notes_f');
        }
        return $this->response('exist_notes_t',NoteResource::collection($notes));
    }

    public function userNotes($id)
    {
        $notes=Note::where('user_id',$id)->get();
        if(is_null($notes)){
            return $this->response('exist_notes_f');
        }
    return $this->response('exist_notes_s',NoteResource::collection($notes));
    }

    public function store(NoteRequest $request)
    {
        $input=$request->all();

        $user=Auth::user();
        $input['user_id']=$user->id;
        $note=Note::create([
            'title'=>$input['title'],
            'description'=>$input['description'],
            'user_id'=>$input['user_id'],
        ]);
        return $this->response('add_note_s',$note);
    }


    public function show(NoteIdRequest $request)
    {
        $note=Note::find($request->id);
        if(is_null($note)){
            return $this->response('exist_note_f');
        }
        return $this->response('exist_note_s',new NoteResource($note));
    }


    public function update(NoteUpdateRequest $request)
    {
        $input=$request->all();
        $validator=Validator::make($input,[
            'title'=>'required',
            'description'=>'required',
        ]);
        if($validator->fails()){
            return $this->response('failed',$validator->errors());
        }
        $note=Note::find($request->id);
        if(is_null($note))
        {
            return $this->response("exist_note_f");
        }
        if($note->user_id!=Auth::id())
        {
            return $this->response("authorization_f");
        }
        $note->title=$input['title'];
        $note->description=$input['description'];
        $user=Auth::user();
        $note['user_id']=$user->id;
        $note->save();

        return $this->response('update_note_s',new NoteResource($note));

    }


    public function destroy(NoteIdRequest $request)
    {

        //return "S";
        $note=Note::find($request->id);

        if(is_null($note))
        {
            return $this->response("exist_note_f");
        }
        if($note->user_id!=Auth::id())
        {
            return $this->response("authorization_f");
        }
        $note->delete();
        return $this->response('delete_note_s',new NoteResource($note));
    }
}
