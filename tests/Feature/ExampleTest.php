<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeOkWhenSeeIndexPage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('產品分類');
        $response->assertSee('管理員頁面');
        $response->assertSee('聯絡我們');
        $response->assertSee('查看購物車');
        $response->assertSee('回首頁');
    }
}
