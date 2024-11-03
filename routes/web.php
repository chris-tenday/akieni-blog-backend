<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogData;

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

Route::get("welcome",function(){
   return view("welcome");
});
