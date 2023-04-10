<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use function PHPUnit\Framework\assertInfinite;

class SignupControllerTest extends TestCase
{
    /** @test */
    function ユーザ登録画面が表示できる()
    {
        $this->get('signup')
            ->assertOk();
    }

    /** @test */
    function ユーザ登録できる()
    {
        // データ検証
        // DBに保存
        // ログインされてからマイページにリダイレクト

        $validData = [
            'name' => '太郎',
            'email' => 'aaa@bbb.com',
            'password' => 'hogehoge',
        ];

        // $validData = User::factory()->make()->toArray();
        // $validData = User::factory()->raw();
        // dd($validData);
        // validData = User::factory()->validData();

        $this->post('signup', $validData)
            ->assertOk();

        unset($validData['password']);

        $this->assertDatabaseHas('users', $validData);

        $user = User::firstwhere($validData);
        // $this->assertNotNull($user);

        $this->assertTrue(Hash::check('hogehoge', $user->password));
    }

    /** @test */
    function 不正なデータではユーザ登録できない()
    {
        $url = 'signup';

        // $this->post($url, [])
        //     ->assertRedirect();

        $this->post($url, ['name' => ''])->assertInvalid(['name' => '指定']);

        $this->post($url, ['name' => str_repeat('あ', 21)])->assertInvalid(['name' => '20文字']);
        $this->post($url, ['name' => str_repeat('あ', 20)])->assertvalid('name');
    }
}
