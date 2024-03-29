<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use File;
use Storage;
use Hash;
use Auth;

class AdminController extends Controller
{
    public function create(Request $request){
       
            $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('user_token')->plainTextToken;
               
                   if($request->hasFile('photo')){
            
                    $ext = $request->file('photo')->extension();
                    
                    $image_name = 'image';
                   
                    
                    $filename = 'image-' . time() . '.' . $ext;
                
                 $path = $request->file('photo')->storeAs('profile-photo', $filename);
                 $image_url = Storage::url($path);
          
                 $data = $this->getDimension($path);
                 $width = $data['width'];
                 $height = $data['height'];
    
                
              $user->photo()->create([
                    "photo_name" => $filename,
                    "photo_path" => $path,
                    "photo_url" => $image_url,
                    "photo_width" => $width,
                    "photo_height" => $height
                ]);
    
           }

           if($user){
               return response()->json([
                "token" => $token,
                "user" => $user,
               ]);
           }
    }

    public function login(Request $request){


        $user = User::where('email',$request->email)->with('photo')->first();
     
       if(Hash::check($request->password, $user->password)){
           $token = $user->createToken('user_token')->plainTextToken;
                return response()->json([
                    "token" => $token,
                    "user" => $user
                ]);
        }
        else{
            return response()->json([
                "message" => "Wrong credentials"
            ]);
        }

    }

    public function getAll(){
        $users = User::all();

        return response()->json([
            "user" => $users
        ]);
    }

    public function logout(){
        
        Auth::user()->tokens()->delete();

        Auth::user()->currentAccessToken()->delete();
    }

  
    public function updateProfile(Request $request){

        $user = Auth::user()->first();

          $isUpdated =  Auth::user()->update([
                "name" => $request->name,
                "email" => $request->email
            ]);

            if($request->hasFile('photo')){
            
                $ext = $request->file('photo')->extension();
                
                $image_name = 'image';
               
                
                $filename = 'image-' . time() . '.' . $ext;
            
             $path = $request->file('photo')->storeAs('profile-photo', $filename);
             $image_url = Storage::url($path);
      
             $data = $this->getDimension($path);
             $width = $data['width'];
             $height = $data['height'];

            
          $user->photo()->update([
                "photo_name" => $filename,
                "photo_path" => $path,
                "photo_url" => $image_url,
                "photo_width" => $width,
                "photo_height" => $height
            ]);

       }

            return $user->with('photo')->first();
     }

    public function updatePassword(Request $request){
           
           $user = Auth::user()->update([
                "password" => Hash::make($request->password),
            ]);

            if($user){

            return Auth::user()->with('photo')->first();
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
