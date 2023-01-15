<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Catagory;
use App\models\Subcatagory;

class CatagoryController extends Controller
{

    public function index(){
          $catagory = Catagory::all()->get();

          return response()->json([
            "catagories" => $catagory
          ]);
    }

    public function create(Request $request){
         $catagories = Catagory::create([
                    'catagory_name' => $request->catagory_name,
                    'show_on_menu' => $request->show_on_menu,
                    'catagory_order' => $request->catagory_order
         ]);

         return response()->json([
            "catagories" => $catagories
         ]);
    }

  
    public function edit(Request $request,$id){
        
        $catagory = Catagory::find($id);

         $edited = $catagory->update([
            'catagory_name' => $request->catagory_name,
            'show_on_menu' => $request->show_on_menu,
            'catagory_order' => $request->catagory_order
          ]);
     }

     public function destroy($id){

        $catagory = Catagory::find($id);
         $catagory->delete();
         $catagory->subCatagories()->delete();
     }

}
