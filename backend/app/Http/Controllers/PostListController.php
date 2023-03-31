<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostListController extends Controller
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
}
