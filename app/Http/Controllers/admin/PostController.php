<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subcatagory;
use App\Models\Post;
use App\Models\Tag;
use Storage;
use Auth;
use File;
use Stevebauman\Location\Facades\Location;

class PostController extends Controller
{
    public function index(){

       $posts = Post::with('subCatagory','catagory','author')->get();

       return response()->json([
            "posts" => $posts
       ]);

        // if (Auth::user()->cannot('view', $posts)) {
        //     abort(403);
        // }
 
    }

    public function recentNews(){
        $recent_news = Post::orderBy('id','desc')->get();
        return response()->json([
            "recent_news" => $recent_news
        ]);
    }

    public function popularNews(){

        $popular_news = Post::orderBy('visitors','desc')->get();
        return response()->json([
            "popular_news" => $popular_news
        ]);
    }

    public function create(Request $request){

     
        $posts = Post::create(
              [
                        'admin_id' => Auth::user()->id,
                        'post_title' => $request->post_title,
                        'post_detail' => $request->post_detail,
                        'catagory_id' => $request->catagory_id,
                        'sub_catagory_id' => $request->sub_catagory_id,
                        'is_sharable' => $request->is_sharable,
                        'is_commentable' => $request->is_commentable
              ]);

              if($request->hasFile('photo')){
            
                $ext = $request->file('photo')->extension();
                
             $image_name = 'image';
             $filename = 'image-' . time() . '.' . $ext;
                
             $path = $request->file('photo')->storeAs('photos', $filename);
             $image_url = Storage::url($path);
             $image_url = Storage::url($path);
            
             $data = $this->getDimension($path);
             $width = $data['width'];
             $height = $data['height'];


             $posts->photo()->create(
                   [
                        "photo_name" => $filename,
                        "photo_path" => $path,
                        "photo_url" => $image_url,
                        "photo_width" => $width,
                        "photo_height" => $height
                  ]
        );

       $tags_array = explode(', ',$request->tags);
       for($i = 0; $i < count($tags_array); $i++){
           $tags = $posts->tags()->create(
               [
                    'tag_name' => $tags_array[$i]
                ]);
             }
          }
       
    }


    public function edit(Request $request,$id){

        $post = Post::find($id);
        $post->update(
            [
                    'post_title' => $request->post_title,
                    'post_detail' => $request->post_detail,
                    'catagory_id' => $request->catagory_id,
                    'sub_catagory_id' => $request->sub_catagory_id,
                    'is_sharable' => $request->is_sharable,
                    'is_commentable' => $request->is_commentable
        ]);

        if($request->hasFile('photo')){
                
            $ext = $request->file('photo')->extension();
            $image_name = 'image';
            $filename = 'image-' . time() . '.' . $ext;
            $path = $request->file('photo')->storeAs('photos', $filename);
            $image_url = Storage::url($path);
            $image_url = Storage::url($path);
            
            $data = $this->getDimension($path);
            $width = $data['width'];
            $height = $data['height'];

            $post->photo()->update(
                [
                    "photo_name" => $filename,
                    "photo_path" => $path,
                    "photo_url" => $image_url,
                    "photo_width" => $width,
                    "photo_height" => $height
                ]
            );

            
    if(!$post->tags->count()){
        $tags_array = explode(',',$request->tags);
       for($i = 0; $i < count($tags_array); $i++){
           $tags = $post->tags()->create([
                 'tag_name' => $tags_array[$i]
           ]);
        }
    }
   
        $tags_array = explode(', ',$request->tags);
        for($i = 0; $i < count($tags_array); $i++){
        $tags = $post->tags()->update(
            [

            'tag_name' => $tags_array[$i]

                ]
             );
          }
       }
   }

    public function destroy($id){
        
        $post = Post::find($id); 

        if(Auth::user()->cannot('delete',$post)){
            abort(403);
        }
        else{
            $post->delete();
            $post->tags()->delete();
        }
    }

    public static function getDimension($path){
        [$width,$height] = getimagesize(Storage::path($path));

        $data = [
            "width" => $width,
            "height" => $height
        ];
         return $data; 
    }

    
    public function postLike(Request $request,$id){

         $post = Post::find($id);
         $liker = $post->likes->where('user_id',Auth::user()->id)->first();

        if(!$liker){
          $like = $post->likes()->create([
            'user_id' => Auth::user()->id,
            'is_liked' => true
           ]);
        }
        else{

         $like = $liker->update([
            'is_liked' => !$liker->is_liked
          ]);
        }

        if($like){
        
        }
    }

    public function postComment(Post $post, Request $request){
          $post->comments()->create([
            'user_id' => Auth::user()->id,
            'comment' => $request->comment,
          ]);

    }
}
