<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * This controller is responsible for talking directly to the jsonPlaceHolder api.
 */
class BlogData extends Controller
{
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
            return $response->json();

        }
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

        if($response->successful())
        {
            /**
             * Return the api response
             */
            $data=$response->json();
            $data['status']="success"; /** add this property to the response */

            return response()->json($data);
        }

        /**
         * If the request failed m return failed as response status.
         */
        return response()->json([
            "status"=>"failed"
        ]);

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
}
