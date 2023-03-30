@extends('layouts.index')

@section('content')

<h1>ブログ一覧(blog list)</h1>

<ul>
    @foreach($posts as $post)
    <li>{{ $post->title }}　{{ $post->user->name }}　
        （{{ $post->comments_count }}件のコメント）</li>
    @endforeach
</ul>

@endsection
