<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Post;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    /**
     * @test
     * Show blog list on Top Page
     */
    function TOPページで、プログ一覧が表示される()
    {
        $post1 = Post::factory()->hasComments(3)->create(['title' => 'ブログのタイトル1']);
        $post2 = Post::factory()->hasComments(5)->create(['title' => 'ブログのタイトル2']);
        Post::factory()->hasComments(1)->create();


        $this->get('/')
            ->assertOk()
            ->assertSee('ブログのタイトル1')
            ->assertSee('ブログのタイトル2')
            ->assertSee($post1->user->name)
            ->assertSee($post2->user->name)
            ->assertSee('（3件のコメント）')
            ->assertSee('（5件のコメント）')
            ->assertSeeInOrder([
                '（5件のコメント）',
                '（3件のコメント）',
                '（1件のコメント）',
            ]);


        // $post1 = Post::factory()->create();
        // $post2 = Post::factory()->create();

        // $this->get('/')
        //     ->assertOk()
        //     ->assertSee($post1->title)
        //     ->assertSee($post2->title);
    }

    /**
     * @test
     */
    function ブログの一覧で、非公開のブログは表示させない()
    {
        $post1 = Post::factory()->closed()->create([
            'title' => 'これは非公開のブログです。'
        ]);
        $post2 = Post::factory()->create([
            'title' => 'これは公開済みのブログです'
        ]);

        $this->get('/')
            ->assertDontSee('これは非公開のブログです')
            ->assertSee('これは公開済みのブログです');
    }

    /**
     * @test
     */
    function ブログの詳細画面を表示させる()
    {
        $post = Post::factory()->create();

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($post->user->name);
    }

    /**
     * @test
     */
    function ブログで非公開の詳細画面は表示されない()
    {

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
