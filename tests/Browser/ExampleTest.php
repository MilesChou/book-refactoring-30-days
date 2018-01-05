<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ExampleTest extends DuskTestCase
{
    /**
     * @test
     */
    public function shouldBeOkWhenSeeIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('產品分類')
                ->assertSee('管理員頁面')
                ->assertSee('聯絡我們')
                ->assertSee('查看購物車')
                ->assertSee('回首頁')
                ->clickLink('管理員頁面')
                ->assertSee('統計功能')
                ->clickLink('查看購物車')
                ->assertSee('購物清單');
        });
    }

    /**
     * @test
     */
    public function shouldBeOkWhenSeeAdminPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin.php')
                ->assertSee('管理功能')
                ->assertSee('統計功能')
                ->clickLink('商品管理')
                ->assertSee('庫存')
                ->clickLink('訂單管理')
                ->assertSee('訂單列表');
        });
    }
}
