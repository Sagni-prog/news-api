<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhotoGallery;
use Storage;
use File;
use Auth;

class GalleryCOntroller extends Controller
{
    public function index(){
      $photo_gallaries = PhotoGallery::with('photo')->get();
    }
    public function create(Request $request){
        if($request->hasFile('photo')){
            
            $ext = $request->file('photo')->extension();
            $filename = 'gallery-' . time() . '.' . $ext;
            
         $path = $request->file('photo')->storeAs('photo-gallery', $filename);
         $image_url = Storage::url($path);
       
        //   $image_url = Storage::url($path);

          $data = $this->getDimension($path);
          $width = $data['width'];
          $height = $data['height'];


          $gallery = PhotoGallery::create([
            'photo_title' => $request->photo_title,
            'photo_description' => $request->photo_description
         ]);

         $gallery->photo()->create([
            "photo_name" => $filename,
            "photo_path" => $path,
            "photo_url" => $image_url,
            "photo_width" => $width,
            "photo_height" => $height
           ]);
        }
    }

    public function edit(Request $request,$id){
      if($request->hasFile('photo')){
            
        $ext = $request->file('photo')->extension();
        $filename = 'gallery-' . time() . '.' . $ext;
        
     $path = $request->file('photo')->storeAs('photo-gallery', $filename);
     $image_url = Storage::url($path);
   
      $data = $this->getDimension($path);
      $width = $data['width'];
      $height = $data['height'];


      $photo_gallary = PhotoGallery::where('id',$id)->with('photo')->first();
      // $photo_gallary = PhotoGallery::find($id)->with('photo')->first();
      
      $gallery = $photo_gallary->update([
        'photo_title' => $request->photo_title,
        'photo_description' => $request->photo_description
     ]);

     $photo_gallary->photo()->update([
        "photo_name" => $filename,
        "photo_path" => $path,
        "photo_url" => $image_url,
        "photo_width" => $width,
        "photo_height" => $height
       ]);
    }
    }

    public function galleryComment(PhotoGallery $photo, Request $request){
         $photo->comments()->create([
            'user_id' => Auth::user()->id,
            'comment' => $request->comment,
          ]);
    }

    public function galleryLike(PhotoGallery $photo){
        $liker = $photo->likes->where('user_id',Auth::user()->id)->first();
      
        if(!$liker){
          $like = $photo->likes()->create([
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
            return back();
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
}
