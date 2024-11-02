<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * This controller is responsible for talking directly to the jsonPlaceHolder api.
 */
class BlogData extends Controller
{
    /**
     * The base url of jsonPlaceHolderApi.
     * @var string
     */
    private $baseUrl="https://jsonplaceholder.typicode.com";


    /**
     * @param int $lastFetchedPostId => the post ID of the last post that was fetched by the frontend.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPosts(int $lastFetchedPostId)
    {
        /**
         * Total posts to retrieve starting from the last fetched post.
         */
        $totalPosts=5;
        $data=[]; /** array to store the posts retrieved */

        for($i=1; $i<=$totalPosts; $i++)
        {
            /**
             * Calculate or resolve the postID of the post to retrieve.
             */
            $postId=$lastFetchedPostId + $i;

            $data[]=$this->getSinglePost(postId: $postId);
        }

        /**
         * Return the data to the client side.
         */
        return response()->json($data);
    }

    /**
     * Method to get a single post from its ID.
     * @param int $postId
     * @return mixed|void
     */
    private function getSinglePost(int $postId)
    {
        $response=Http::get($this->baseUrl."/posts/".$postId);
        if($response->successful())
        {
            $post=$response->json();
            $post['author']=$this->getPostAuthor(userId: $post['userId']);

            return $post;

        }
    }

    /**
     * Method to get a post author.
     * @param int $userId
     * @return string|null
     */
    private function getPostAuthor(int $userId) : ?string
    {
        $response=Http::get($this->baseUrl."/users/".$userId);

        if($response->successful())
        {
            return $response->object()->name;
        }
        return null;
    }

    /**
     * Used to get a single post.
     * @param int $postId
     * @return mixed|void
     */
    public function getPost(int $postId)
    {
        $response=$this->getSinglePost($postId);
    }

    /**
     * Get comments of post.
     * @return array
     */
    public function getComments(int $postId)
    {
        $response=Http::get($this->baseUrl."/posts/$postId/comments");

        if($response->successful())
        {
            return $response->json();
        }

        return [];
    }

    /**
     * Method to register a comment post.
     */
    public function commentPost(Request $request)
    {
        try
        {
            $this->checkMissingData(["name","postId","email", "body"]);
        }
        catch (\Exception $e)
        {
            return response()->json([
                $e->getMessage() => "missing"
            ]);
        }

       $response=Http::post($this->baseUrl."/comments",[
           "postId"=>$request->input("postId"),
           "name"=>$request->input("name"),
           "email"=>$request->input("email"),
           "body"=>$request->input("body")
       ]);

        /**
         * This is used to differentiate when the request fails or succeeds
         */
        $responseData=["status"=>"failed"];

        if($response->successful())
        {
            $responseData['status']="success";

            $responseData=array_merge($responseData,$response->json());
        }

        /**
         * REturn the constructed response.
         */
        return response()->json($responseData);

    }

    private function checkMissingData(array $requiredData)
    {
        $request=request();
        foreach($requiredData as $d)
        {
            if(!$request->has($d))
            {
                throw new \Exception($d);
            }
        }
    }

    /**
     * Publish a new post to the jsonPlaceHolder service.
     * @param Request $request
     */
    public function publishPost(Request $request)
    {
        $requiredData=["title","body","userId"];
        try
        {
            $this->checkMissingData($requiredData);
        }
        catch(\Exception $e){
            return response()->json([
                $e->getMessage() => "missing"
            ]);
        }

        /**
         * Make post request to the api.
         */
        $response=Http::post($this->baseUrl."/posts",[
            "title"=>$request->input("title"),
            "body"=>$request->input("body"),
            "userId"=>$request->input("userId")

        ]);

        /**
         * This is used to differentiate when the request fails or succeeds
         */
        $data=["status"=>"failed"];

        if($response->successful())
        {
            $data['status']="success";
            $d=$response->json();
            $data=array_merge($data,$d);
        }

        /**
         * Return the constructed response.
         */
        return response()->json($data);
    }
}
