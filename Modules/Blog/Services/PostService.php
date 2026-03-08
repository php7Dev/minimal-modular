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

                // Example method
                public function all()
                {
                    return $this->Post::all();
                }
            }
            