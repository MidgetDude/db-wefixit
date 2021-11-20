<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class PostsApiController extends Controller
{
    public function index(){

        $posts = Post::all();
        return response()->json([
            'status'=>200,
            'posts'=>$posts,
        ]);
    }

    public function getPost($postid){

        if(Post::where('id', $postid)->exists()){

            $post = Post::where('id', $postid)->get();
            return response($post, 200);

        } else {

            return response()->json(["message" => "Post not found"], 404);

        }
    }
    
    public function getPostComments($postid, Comment $comments){

        if(Post::where('id', $postid)->exists()){

            return response(array(
                $post = Post::where('id', $postid)->get(),
                $comments = Comment::where('post_id', $postid)->get()), 200);
        } else {
            return response()->json([
                "message" => "Post not found"
            ], 404);
        }
    }

    public function store(){
        request()->validate([
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required',
        ]);

        $isGuest = auth()->guest();

        if(! $isGuest){
            $user_id = auth()->user()->id;

            if (Category::where('id', request('category_id'))){

                return Post::create([
                    'title' => request('title'),
                    'content' => request('content'),
                    'category_id' => request('category_id'),
                    'user_id' => $user_id,]);
            }
            else{
                return response()->json(["message" => "Category not found"], 404);
            }
        }
        else{
            return response()->json([
                "message" => "Unauthorized"
            ], 401);
        }
    }

    public function update(Request $request, $postid){

        $isGuest = auth()->guest();

        if(! $isGuest){

            $user_id = auth()->user()->id;
            $user_role = auth()->user()->role;
            
            if (Post::where('id', $postid)->exists()){

                $post = Post::find($postid);

                if($user_id == $post -> user_id || $user_role == 1){
                    $post->title = is_null($request->title) ? $post->title : $request->title;
                    $post->content = is_null($request->content) ? $post->content : $request->content;
                    $post->category_id = $post->category_id;
                    $post->user_id = $post->user_id;
                    $post->save();
                    
                    return response()->json(["message" => "Post update finished", "post" => $post], 200);
                }
                else{
                    return response()->json(["message" => "Unauthorized"], 401);
                }
            }
            else{
                return response()->json(["message" => "Post missing"], 404);
            }
        }
        else{
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }

    public function destroy($postid){

        $isGuest = auth()->guest();

        if(! $isGuest){
            $user_id = auth()->user()->id;
            $user_role = auth()->user()->role;

            if(Post::where('id', $postid)->exists()){
                $post = Post::find($postid);
                if ($user_id == $post->user_id || $user_role == 1){
                    $post->delete();
                    return response()->json(["message" => "post deleted"], 202);
                }
                else{
                    return response()->json(["message" => "Unauthorized"], 401);
                }
            }
            else{
                return response()->json(["message" => "post not found"], 404);
            }
        }
        else{
            return response()->json(["message" => "Unauthorized"], 401);
        }
    }
}
