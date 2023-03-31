<?php

namespace Tests\Feature\Models;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;

class PostTest extends TestCase
{
    /**
     * @test
     */
    function userリレーションを返す()
    {
        $post = Post::factory()->create();


        $this->assertInstanceOf(User::class, $post->user);
    }

    /**
     * @test
     */
    function commnetレレーションのテスト()
    {
        $post = Post::factory()->create();

        $this->assertInstanceOf(Collection::class, $post->comments);
    }

    /**
     * @test
     */
    function ブログの公開・非公開のscope()
    {
        $post1 = Post::factory()->closed()->create();
        $post2 = Post::factory()->create();

        $posts = Post::onlyOpen()->get();

        $this->assertFalse($posts->contains($post1));
        $this->assertTrue($posts->contains($post2));
    }
}
