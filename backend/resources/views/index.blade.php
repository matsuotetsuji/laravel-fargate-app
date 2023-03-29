@extends('layouts.index')

@section('content')

<h1>ブログ一覧(blog list)</h1>

<ul>
    @foreach($posts as $post)
    <li>{{ $post->title }} {{ $post->user->name }}</li>
    @endforeach
</ul>

@endsection
