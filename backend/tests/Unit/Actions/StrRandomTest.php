<?php

namespace Tests\Unit\Actions;

use App\Actions\StrRandom;
use PHPUnit\Framework\TestCase;

class StrRandomTest extends TestCase
{
    /**
     * @test
     */
    function StrRandom_正しい文字数を返す()
    {
        class_alias(\Illuminate\Support\Str::class, \Str::class);
        $random = new StrRandom();

        $ret1 = $random->get(8);
        $ret2 = $random->get(10);

        $this->assertTrue(strlen($ret1) === 8);
        $this->assertTrue(strlen($ret2) === 10);
    }

    /**
     * @test
     */
    function StrRandom_ランダムの文字列を返す()
    {
        $random = new StrRandom();

        $ret1 = $random->get(8);
        $ret2 = $random->get(8);

        $this->assertFalse($ret1 === $ret2);
    }
}
