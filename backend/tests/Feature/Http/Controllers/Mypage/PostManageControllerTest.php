<?php

namespace Tests\Feature\Http\Controllers\Mypage;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class PostManageControllerTest extends TestCase
{

    // use WithoutMiddleware;

    /*
    * @test
    */
    function ゲストはブログを管理できない()
    {
        $loginUrl = 'mypage/login';

        $this->get('mypage/posts')->assertRedirect($loginUrl);
        $this->get('mypage/posts/create')->assertRedirect($loginUrl);
        $this->post('mypage/posts/create', [])->assertRedirect($loginUrl);
        $this->get('mypage/posts/edit/1')->assertRedirect($loginUrl);
        $this->post('mypage/posts/edit/1', [])->assertRedirect($loginUrl);
        $this->delete('mypage/posts/delete/1', [])->assertRedirect($loginUrl);
    }

    /**
     * @test
     */
    public function マイページ。ブログ一覧で自分のデータのみ表示される()
    {
        $user = $this->login();

        $other = Post::factory()->create();
        $mypost = Post::factory()->create(['user_id' => $user->id]);

        // 認証済みの場合
        // $user = User::factory()->create();
        // $this->actingAs($user)
        //     ->get('mypage/posts')
        //     ->assertOk();

        $this->get('mypage/posts')
            ->assertOk()
            ->assertDontSee($other->title)
            ->assertSee($mypost->title);
    }

    /** @test */
    function マイページ、ブログの新規登録ページが開ける()
    {
        $this->login();

        $this->get('mypage/posts/create')
            ->assertOk();
    }

    /**
     * @test
     */
    function マイページ、ブログを新規登録できる、公開の場合()
    {
        // $this->withoutExceptionHandling();

        [$taro, $jiro, $me] = User::factory(3)->create();

        $this->login($me);

        $validData = [
            'title' => '私のブログタイトル',
            'body' => '私のブログ本文',
            'status' => '1',
        ];

        //$this->post('mypage/posts/create', $validData)
        //    ->assertRedirect('mypage/posts/edit/1') // SQLiteのインメモリ

        $response = $this->post('mypage/posts/create', $validData);

        $post = Post::first();

        $response->assertRedirect('mypage/posts/edit/'.$post->id);

        $this->assertDatabaseHas('posts', array_merge($validData, ['user_id' => $me->id]));
    }

    /**
     * @test
     */
    function マイページ、ブログを新規登録できる、非公開の場合()
    {
        [$taro, $jiro, $me] = User::factory(3)->create();

        $this->login($me);

        $validData = [
            'title' => '私のブログタイトル',
            'body' => '私のブログ本文',
            // 'status' => '1',
        ];

        $this->post('mypage/posts/create', $validData);

        $post = Post::first();

        $this->assertDatabaseHas('posts', array_merge($validData, [
            'user_id' => $me->id,
            'status'  => 0,
        ]));

    }

    /**
     * @test
     */
    function マイページ、ブログの登録時の入力チェック()
    {
        $url = 'mypage/posts/create';

        $this->login();

        $this->from($url)->post($url, [])
            ->assertRedirect($url);

        app()->setLocale('testing');

        $this->post($url, ['title' => ''])->assertInvalid(['title' => 'required']);
        $this->post($url, ['title' => str_repeat('a', 256)])->assertInvalid(['title' => 'max']);
        $this->post($url, ['title' => str_repeat('a', 255)])->assertvalid('title');
        $this->post($url, ['body' => ''])->assertInvalid(['body' => 'required']);
    }

    /**
     * @test
     */
    function 自分のブログの編集画面は開ける()
    {
        $post = Post::factory()->create();

        $this->login($post->user);

        $this->get('mypage/posts/edit/'.$post->id)
            ->assertOk();
    }

    /**
     * @test
     */
    function 他人様のブログの編集画面は開けない()
    {
        $post = Post::factory()->create();

        $this->login();

        $this->get('mypage/posts/edit/'.$post->id)
            ->assertForbidden();
    }

    /**
     * @test
     */
    function 自分のブログは変更できる()
    {
        $validData = [
            'title' => '新タイトル',
            'body'  => '新本文',
            'status'=> '1',
        ];

        $post = Post::factory()->create();

        $this->login($post->user);

        $this->post('mypage/posts/edit/'.$post->id, $validData)
            ->assertRedirect('mypage/posts/edit/'.$post->id);

        $this->get('mypage/posts/edit/'.$post->id)
            ->assertSee('ブログを更新しました');

        // DBに登録されている事は確認したが、新規で追加されたかもしれない。なので、不完全と言えば、不完全
        $this->assertDatabaseHas('posts', $validData);

        $this->assertCount(1, Post::all());
        $this->assertSame(1, Post::count());


        // 項目が少ない時は fresh()を使って
        $this->assertSame('新タイトル', $post->fresh()->title);
        $this->assertSame('新本文', $post->fresh()->body);
        $this->assertSame(1, $post->fresh()->status);

        // 項目が多い時は refresh()を使って
        $post->refresh();
        $this->assertSame('新タイトル', $post->title);
        $this->assertSame('新本文', $post->body);
        $this->assertSame(1, $post->status);

    }

    /**
     * @test
     */
    function 他人様のブログは更新できない()
    {
        $validData = [
            'title' => '新タイトル',
            'body'  => '新本文',
            'status'=> '1',
        ];

        $post = Post::factory()->create(['title' => '元のブログタイトル']);

        $this->login();

        $this->post('mypage/posts/edit/'.$post->id, $validData)
            ->assertForbidden();

        $this->assertSame('元のブログタイトル', $post->fresh()->title);

    }

    /**
     * @test
     */
    function 自分のブログは削除できる、かつ付随するコメントも削除される()
    {
        $post = Post::factory()->create();

        $myPostComment = Comment::factory()->create(['post_id' => $post->id]);
        $otherPostComment = Comment::factory()->create();

        $this->login($post->user);

        $this->delete('mypage/posts/delete/'.$post->id)
            ->assertRedirect('mypage/posts');

        // ブログの削除の確認
        // ver.8.61 以降は assertModelMissin()を使いましょう
        $this->assertModelMissing($post);

        // コメントの削除の確認
        $this->assertModelMissing($myPostComment);
        $this->assertModelExists($otherPostComment);

    }

    /**
     * @test
     */
    function 他人様のブログは削除できない()
    {
        $post = Post::factory()->create();

        $this->login();

        $this->delete('mypage/posts/delete/'.$post->id)
            ->assertForbidden();

        $this->assertModelExists($post);
    }

}
