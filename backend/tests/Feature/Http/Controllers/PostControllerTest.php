<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Carbon;
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
    function ブログの詳細画面を表示でき、コメントが古い順に表示される()
    {
        $post = Post::factory()->create();

        Comment::factory()->create([
            'created_at' => now()->sub('2 days'),
            'name' => 'コメント太郎',
            'post_id' => $post->id,
        ]);
        Comment::factory()->create([
            'created_at' => now()->sub('3 days'),
            'name' => 'コメント次郎',
            'post_id' => $post->id,
        ]);
        Comment::factory()->create([
            'created_at' => now()->sub('1 days'),
            'name' => 'コメント三郎',
            'post_id' => $post->id,
        ]);

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($post->user->name)
            ->assertSeeInOrder(['コメント次郎', 'コメント太郎', 'コメント三郎']);

    }

    /**
     * @test
     */
    function ブログで非公開の詳細画面は表示されない()
    {
        $post = Post::factory()->closed()->create();

        $this->get('posts/'.$post->id)
            ->assertForbidden();
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

    /*
    * @test
    */
    function ブログで非公開時にTrueを返し、公開時にfalseを返す()
    {
        $open = Post::factory()->create();

        $closed = Post::factory()->closed()->create();

        $this->assertFalse($open->isClosed());
        $this->assertTrue($closed->isClosed());
    }

    /**
     * @test
     */
    function クリスマスの日はメリークリスマスと表示される()
    {
        $post = Post::factory()->create();
        Carbon::setTestNow('2020-12-24');

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertDontSee('メリークリスマス');

        Carbon::setTestNow('2020-12-25');

        $this->get('posts/'.$post->id)
            ->assertOk()
            ->assertSee('メリークリスマス');

    }

}
