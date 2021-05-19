<?php

namespace App\Http\Middleware;

use App\Repositories\Contracts\Posts as PostsContract;
use Closure;
use Illuminate\Http\Request;

class PostExists
{
    private $contract;
    public function __construct(PostsContract $post_contract)
    {
        $this->contract = $post_contract;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $post_id = intval($request->route('post') ?? 0);
        $post = $this->contract->fetchSingleEntry($post_id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        // post is good, continue with the request...
        return $next($request);
    }
}
