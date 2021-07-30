<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NoteController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('image',[NoteController::class,'receiveImage']);


    /****************************** User Route *********************************/
Route::get('users',[UserController::class,'index']);

    /******************************Auth Route *********************************/
Route::post('signup',[AuthController::class,'signup']);

Route::post('signin',[AuthController::class,'signin']);


Route::group(['middleware'=>'auth:api'],function(){
    Route::post('logout',[AuthController::class,'logout']);
    Route::post('update',[AuthController::class,'UpdateProfile']);
    Route::post('setImage',[AuthController::class,'updateImage']);

    /***************************** Note Route ****************************/
    Route::group(['prefix'=>'notes'], function(){
        Route::get('',[NoteController::class,'index']);
        Route::get('user',[NoteController::class,'notesUser']);
        Route::post('store',[NoteController::class,'store']);
        Route::get('show',[NoteController::class,'show']);
        Route::post('update',[NoteController::class,'update']);
        Route::delete('destroy',[NoteController::class,'destroy']);
        Route::get('mynotes',[NoteController::class,'myNotes']);
        Route::get('filteredmynotes',[NoteController::class,'filteredMyNotes']);


    });



});

