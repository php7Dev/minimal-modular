<?php

namespace Modules\Blog\Middleware;

use Closure;
use Illuminate\Http\Request;

class PostMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
