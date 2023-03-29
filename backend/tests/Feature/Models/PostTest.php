<?php

namespace Tests\Feature\Models;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

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
}
