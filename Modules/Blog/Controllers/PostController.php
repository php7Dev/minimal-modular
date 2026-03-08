<?php

namespace Modules\Blog\Controllers;

use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index()
    {
        return view('blog::post');
    }
}
