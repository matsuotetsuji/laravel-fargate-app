<?php

namespace App\Http\Controllers;

use App\Actions\StrRandom;
use App\Models\Post;
use Illuminate\Http\Request;
use Stringable;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->onlyOpen() //->where('status', Post::OPEN)
            ->with('user')
            ->orderByDesc('comments_count')
            ->withCount('comments')
            ->get();
            // n+1問題の対応
            // https://readouble.com/laravel/9.x/ja/eloquent-relationships.html

        return view('index', compact('posts'));
    }

    public function show(Post $post, StrRandom $strRandom)
    {
        // if($post->status == Post::CLOSED) {
        //     abort(403);
        // }

        if ($post->isClosed()){
            abort(403);
        }

        // $random = \Str::random(10);

        $random = $strRandom->get(10);

        return view('posts.show', compact('post', 'random'));
    }
}
