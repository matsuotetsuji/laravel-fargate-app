<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\SignupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('', [PostController::class, 'index']);
Route::get('posts/{post}', [PostController::class, 'show'])
    ->name('posts.show')
    ->whereNumber('post');


Route::get('signup', [SignupController::class, 'index']);
Route::post('signup', [SignupController::class, 'store']);

/**ß
Route::get('/', function () {
    return view('welcome');
});
*/


