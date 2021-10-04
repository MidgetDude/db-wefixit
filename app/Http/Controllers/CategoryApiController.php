<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryApiController extends Controller
{
    public function index(){

        return Category::all();
    }

    public function getCategory($categoryid){
        if(Category::where('id', $categoryid)->exists()){
            $category = Category::where('id', $categoryid)->get();
            return response($category, 200);
        }
        else{
            return response()->json([
                "message" => "Category not found"
            ], 404);
        }
    }

    public function store(){
        request()->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        return Category::create([
            'title'=> request('title'),
            'description' => request('description')
        ]);
    }

    public function update(Request $request, $categoryid){
        if(Category::where('id', $categoryid)->exists()){
            $category = Category::find($categoryid);
            $category->title = is_null($request->title) ? $category->title : $request->title;
            $category->description = is_null($request->description) ? $category->description : $request->description;
            $category->save();

            return response()->json([
                "message" => "category update finished"
            ], 200);
        }
        else{
            return response()->json([
                "message" => "category missing"
            ], 404);
        }
    }
    public function destroy($categoryid){
        if(Category::where('id', $categoryid)->exists()){
            $category = Category::find($categoryid);
            $category->delete();

            return response()->json([
                "message" => "Category removed"
            ], 202);
        }
        else{
            return response()->json([
                "message" => "Category missing"
            ], 404);
        }
    }
}
