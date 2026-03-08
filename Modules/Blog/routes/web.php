<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Controllers\PostController;

Route::get('/blog', [PostController::class,'index']);
