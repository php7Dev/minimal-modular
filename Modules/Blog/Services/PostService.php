<?php

namespace Modules\Blog\Services;

use Modules\Blog\Models\Post;

class PostService
{
    protected $Post;

    public function __construct(Post $Post)
    {
        $this->Post = $Post;
    }

    public function all()
    {
        return $this->Post::all();
    }
}
