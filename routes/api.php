<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/', function (Request $request) {
    return response()->json([
        'message' => 'Server is up and running',
    ], 200);
});

// Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);


Route::group(['prefix' => 'studysama'], function (){

    //Route::post('/password/forgot', [ForgotPasswordController::class, 'sendResetLinkEmail']);

    //user controller
    Route::group(['prefix' => 'users'], function (){
        //auth
        Route::post('store', 'App\Http\Controllers\UserController@store');
        Route::post('login', 'App\Http\Controllers\UserController@login');
        Route::middleware('auth:sanctum')->get('logout', 'App\Http\Controllers\UserController@logout');
        Route::middleware('auth:sanctum')->post('update', 'App\Http\Controllers\UserController@update');
        Route::middleware('auth:sanctum')->get('index_all', 'App\Http\Controllers\UserController@index_all'); 
        Route::middleware('auth:sanctum')->post('index_user', 'App\Http\Controllers\UserController@index_user');
        Route::middleware('auth:sanctum')->post('index_follow', 'App\Http\Controllers\UserController@index_follow');
        Route::middleware('auth:sanctum')->post('update_follow', 'App\Http\Controllers\UserController@update_follow');
        Route::middleware('auth:sanctum')->post('index_user_badge', 'App\Http\Controllers\UserController@index_user_badge');
    });

    //course controller
    Route::group(['prefix' => 'course'], function (){
        Route::middleware('auth:sanctum')->get('index_all', 'App\Http\Controllers\CourseController@index_all');
        Route::middleware('auth:sanctum')->post('index_course', 'App\Http\Controllers\CourseController@index_course');
        Route::middleware('auth:sanctum')->post('index_course_courseid', 'App\Http\Controllers\CourseController@index_course_courseid');
        Route::middleware('auth:sanctum')->post('index_user_course', 'App\Http\Controllers\CourseController@index_user_course');
        Route::middleware('auth:sanctum')->post('store', 'App\Http\Controllers\CourseController@store');
        Route::middleware('auth:sanctum')->post('update', 'App\Http\Controllers\CourseController@update');
        Route::middleware('auth:sanctum')->post('update_user_course', 'App\Http\Controllers\CourseController@update_user_course');
        Route::middleware('auth:sanctum')->post('index_review', 'App\Http\Controllers\CourseController@index_review');
        Route::middleware('auth:sanctum')->post('update_review', 'App\Http\Controllers\CourseController@update_review');
        Route::middleware('auth:sanctum')->post('index_user_course_join', 'App\Http\Controllers\CourseController@index_user_course_join');
    });

    //lesson controller
    Route::group(['prefix' => 'lesson'], function (){
        Route::middleware('auth:sanctum')->get('index_all', 'App\Http\Controllers\LessonController@index_all');
        Route::middleware('auth:sanctum')->post('index_lesson_course', 'App\Http\Controllers\LessonController@index_lesson_course');
        Route::middleware('auth:sanctum')->post('store', 'App\Http\Controllers\LessonController@store');
        Route::middleware('auth:sanctum')->post('update', 'App\Http\Controllers\LessonController@update');
    });

    //resource controller
    Route::group(['prefix' => 'resource'], function (){
        Route::middleware('auth:sanctum')->get('index_all', 'App\Http\Controllers\ResourceController@index_all');
        Route::middleware('auth:sanctum')->post('index_resource_lesson', 'App\Http\Controllers\ResourceController@index_resource_lesson');
        Route::middleware('auth:sanctum')->post('store', 'App\Http\Controllers\ResourceController@store');
        Route::middleware('auth:sanctum')->post('update', 'App\Http\Controllers\ResourceController@update');
    });

    //comment controller
    Route::group(['prefix' => 'comment'], function (){
        Route::middleware('auth:sanctum')->get('index_all', 'App\Http\Controllers\CommentController@index_all');
        Route::middleware('auth:sanctum')->post('index_comment_resource', 'App\Http\Controllers\CommentController@index_comment_resource');
        Route::middleware('auth:sanctum')->post('store', 'App\Http\Controllers\CommentController@store');
        Route::middleware('auth:sanctum')->post('update', 'App\Http\Controllers\CommentController@update');
    });

    //tutorslot controller
    Route::group(['prefix' => 'tutorslot'], function (){
        Route::middleware('auth:sanctum')->get('index_all', 'App\Http\Controllers\TutorSlotController@index_all');
        Route::middleware('auth:sanctum')->post('index_tutorslot_course', 'App\Http\Controllers\TutorSlotController@index_tutorslot_course');
        Route::middleware('auth:sanctum')->post('store', 'App\Http\Controllers\TutorSlotController@store');
        Route::middleware('auth:sanctum')->post('update', 'App\Http\Controllers\TutorSlotController@update');
    });
});
