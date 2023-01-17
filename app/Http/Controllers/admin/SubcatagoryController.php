<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subcatagory;

class SubcatagoryController extends Controller
{
    public function index(){

        $sub_catagories = Subcatagory::with('catagory')->orderBy('sub_catagory_order','asc')->get();

        return response()->json([
            "sub_catagory" => $sub_catagories
        ]);
    }

    public function create(Request $request){
        $sub_catagory = Subcatagory::create( [
            'catagory_id' => $request->catagory_id,
            'sub_catagory_name' => $request->sub_catagory_name,
            'show_on_menu' => $request->show_on_menu,
            'sub_catagory_order' => $request->sub_catagory_order
         ]);

         return response()->json([
            "sub_catagory" => $sub_catagory
         ]);

    }

    public function edit(Request $request,$id){

        $sub_catagory = Subcatagory::find($id);

        $sub_catagory->update([
            'catagory_id' => $request->catagory_id,
            'sub_catagory_name' => $request->sub_catagory_name,
            'show_on_menu' => $request->show_on_menu,
            'sub_catagory_order' => $request->sub_catagory_order
         ]);

        //  return response()->json([
        //     "sub_catagory" => $sub_catagory
        //  ]);
    }

    public function destroy($id){

        $sub_catagory = Subcatagory::find($id);
        $sub_catagory->delete();
    }
    
}
