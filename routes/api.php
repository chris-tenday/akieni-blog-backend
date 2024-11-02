<?php

use App\Http\Controllers\BlogData;
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

/**Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::prefix("posts")->group(function(){

    /**
     * Route to get posts.
     */
    Route::get("/fetch/{lastFetchedPostId}",[BlogData::class,'getPosts']);

    Route::get("/get/{postId}",[BlogData::class,"getPost"]);

    /**
     * Router to publish a new post.
     */
    Route::post("/publish",[BlogData::class,"publishPost"]);

});

Route::prefix("comments")->group(function(){

    Route::get("/get/{postId}",[BlogData::class,"getComments"]);

    /**
     * Route to add a post comment.
     */
    Route::post("/add",[BlogData::class,"commentPost"]);
});

