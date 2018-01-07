<?php

namespace Tests\Browser;

use App\ProductCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

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
    public function shouldSeeNewCategoryWhenCreateNewCategory()
    {
        /** @var ProductCategory $category */
        $category = factory(ProductCategory::class)->create();

        $this->browse(function (Browser $browser) use ($category) {
            $browser->visit('/')
                ->assertSee('產品分類')
                ->assertSee('未分類')
                ->assertSee($category->title);
        });
    }

    /**
     * @test
     */
    public function shouldDontSeeSmartyTagSeeIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertDontSee('<%')
                ->assertDontSee('%>');
        });
    }

    /**
     * @test
     */
    public function shouldBeOkWhenSeeContactPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->assertSeeLink('檢視較大的地圖');
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

    /**
     * @test
     */
    public function shouldBeOkWhenSeeAdminLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/login')
                ->assertSee('管理員登入')
                ->assertSee('帳號')
                ->assertSee('密碼');
        });
    }
}
