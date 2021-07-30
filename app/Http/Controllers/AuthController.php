<?php

namespace App\Http\Controllers;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\SigninRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    public function signup(SignupRequest $request)
    {

        $input=$request->all();
        $input['password']=Hash::make($input['password']);
        $user=User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
        $data = $this->login($user->email,$request->password);
            return $this->response('signin_user_s', $data);
      //  $this->login($user->email,$user->password);
    }

    public function signin(SigninRequest $request)
    {
        $user=User::whereEmail($request->email)->first();
        if($user and Hash::check($request->password,$user->password)){
            $data = $this->login($user->email,$request->password);
            return $this->response('signin_user_s', $data);
        }else{
            return $this->response('invalid_email_or_password',null,404);
        }
    }//end login function
    private function login($email,$password)
    {

        $credentials=['email'=>$email,'password'=>$password];
        if(!Auth::attempt($credentials))
            return $this->response('invalid_email_or_password',null,404);
        $user=Auth::user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token       = $tokenResult->token;
        $token->expires_at=Carbon::now()->addWeeks(4);
        $token->save();
        $data=$this->initializeLoginData($tokenResult,$user);
        return $data;
    }

    private function initializeLoginData($tokenResult,$user)
    {
        return [
            'access_token'=>'Bearer '.$tokenResult->accessToken,
            'expires_at'=>Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'id'=>$user->id,
            'name'=>$user->name,
            'email'=>$user->email,
            'password'=>$user->password
        ];
    }

    public function logout(Request $request)
    {
        if($request->user()->token()->revoke())
        {
            return $this->response('success',200);
        }else{
            return $this->response('failed',404);
        }
    }
    public function UpdateProfile(UpdateRequest $request)
    {

        $input=$request->all();
        $user=User::find($request->id);
        if(is_null($user))
        {
            return $this->response("exist_user_f");
        }
        if( $user->id!=Auth::id())
        {
            return $this->response("authorization_f");
        }
        $user->name=$input['name'];
     //   $user=Auth::user();
        $user->save();

        return $this->response('update_user_s',new UserResource($user));
    }
    public function updateImage(Request $request)
    {

        if($request->hasFile('image')){
            $filename=$request->image->getClientOriginalName();
            $request->image->storeAs('images',$filename,'public');
        }
         $path = public_path() .'/'. 'images/'.$filename;
       return $path;

    //     $imageName = Str::random(40).'.'.'png';
    //     Storage::disk('local')->put($imageName, base64_decode($file_data));

//
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
}//end class
