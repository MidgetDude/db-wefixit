<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use DB;

class CommentsApiController extends Controller
{
    public function index(){
        return $comments = DB::table('comments')->where('post_id', 'postid')->get();
    }

    public function store(){
        request()->validate([
            'author' => 'required',
            'comment_text'=> 'required',
            'post_id'=>'required',
        ]);

        return Comment::create([
            'author'=> request('author'),
            'comment_text' => request('comment_text'),
            'post_id' => request('post_id'),
        ]);
    }

    public function update(Comment $comment){
        request()->validate([
            'author' => 'required',
            'comment_text'=> 'required',
            'post_id'=>'required',
        ]);

        $success = $comment->update([
            'author'=> request('title'),
            'comment_text' => request('content'),
            'post_id' => request('post_id'),
        ]);
    
        return['success' => $success];
    }
    public function destroy(Comment $comment){
        $success = $comment->delete();
        return['success' => $success];
    }
}
