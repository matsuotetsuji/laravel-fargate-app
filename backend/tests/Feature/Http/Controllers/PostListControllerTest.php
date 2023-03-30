<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Post;
use Tests\TestCase;

class PostListControllerTest extends TestCase
{
    /**
     * @test
     * Show blog list on Top Page
     */
    function TOPページで、プログ一覧が表示される()
    {
        $post1 = Post::factory()->hasComments(3)->create(['title' => 'ブログのタイトル1']);
        $post2 = Post::factory()->hasComments(5)->create(['title' => 'ブログのタイトル2']);

        $this->get('/')
            ->assertOk()
            ->assertSee('ブログのタイトル1')
            ->assertSee('ブログのタイトル2')
            ->assertSee($post1->user->name)
            ->assertSee($post2->user->name)
            ->assertSee('（3件のコメント）')
            ->assertSee('（5件のコメント）');


        // $post1 = Post::factory()->create();
        // $post2 = Post::factory()->create();

        // $this->get('/')
        //     ->assertOk()
        //     ->assertSee($post1->title)
        //     ->assertSee($post2->title);
    }

    /**
     * @test
     * watch factory
     */
    function factoryの観察()
    {
        // post = Post::factory()->create();
        // dump($post->toArray());

        // dump(User::get()->toArray());

        $this->assertTrue(true);
    }
}
