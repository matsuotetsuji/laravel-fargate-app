<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostListControllerTest extends TestCase
{
    /**
     * @test
     * Show blog list on Top Page
     */
    function TOPページで、プログ一覧が表示される()
    {
        $this->get('/')
            ->assertOk();
    }
}
