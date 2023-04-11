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

        User::factory()->create(['email' => 'aaa@bbb.net']);

        // $this->post($url, [])
        //     ->assertRedirect();

        app()->setLocale('testing');

        $this->post($url, ['name' => ''])->assertInvalid(['name' => 'required']);
        $this->post($url, ['name' => str_repeat('あ', 21)])->assertInvalid(['name' => 'max']);
        $this->post($url, ['name' => str_repeat('あ', 20)])->assertvalid('name');

        $this->post($url, ['email' => ''])->assertInvalid(['email' => 'required']);
        $this->post($url, ['email' => 'aa@bb@cc'])->assertInvalid(['email' => 'email']);
        $this->post($url, ['email' => 'aa@ああ.cc'])->assertInvalid(['email' => 'email']);
        $this->post($url, ['email' => 'aaa@bbb.net'])->assertInvalid(['email' => 'unique']);

        $this->post($url, ['password' => ''])->assertInvalid(['password' => 'required']);
        $this->post($url, ['password' => 'abcd123'])->assertInvalid(['password' => 'min']);
        $this->post($url, ['password' => 'abcd1234'])->assertvalid(['password' => 'min']);
    }
}
