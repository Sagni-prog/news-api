<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\admin\CatagoryController;
use App\Http\Controllers\admin\SubcatagoryController;
use App\Http\Controllers\admin\PostController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\OnlinePollController;
use App\Http\Controllers\GalleryController;



Route::post('admin/register',[AdminController::class,'create'])->name('register_admin');


Route::post('admin/login',[AdminController::class,'login'])->name('login_admin');


Route::middleware('auth:sanctum')->group(function(){

    Route::get('users',[AdminController::class,'getAll']);

    Route::post('admin/profile/update',[AdminController::class,'updateProfile'])->name('profile_update');
    Route::post('/admin/profile/update-password',[AdminController::class,'updatePassword'])->name('profile_update-password');
    Route::get('/',[AdminController::class,'getAll']);
    Route::post('logout',[AdminController::class,'logout'])->name('user_logout');

    Route::get('catagories',[CatagoryController::class,'index']);
    Route::post('catagory',[CatagoryController::class,'create'])->name('add-catagory');
    Route::post('catagory/catagory-update/{id}',[CatagoryController::class,'edit'])->name('update-catagory');
    Route::post('catagory/catagory-delete/{id}',[CatagoryController::class,'destroy'])->name('delete-catagory');

    Route::get('sub-catagories',[SubcatagoryController::class,'index']);
    Route::post('sub-catagory',[SubcatagoryController::class,'create'])->name('add-sub-catagory');
});

Route::get('posts',[PostController::class,'showPost']);
Route::post('post',[PostController::class,'create'])->name('add_post');