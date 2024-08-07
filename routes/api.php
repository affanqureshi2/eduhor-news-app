<?php

use App\Http\Controllers\Api\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/list',"NewsController@list")->name('list');
Route::get('/single',"NewsController@single")->name('single');
Route::put('/pin', [NewsController::class, 'pin']);
Route::put('/unpin', [NewsController::class, 'unpin']);
Route::get('/getpin', [NewsController::class, 'getPinnedNews']);