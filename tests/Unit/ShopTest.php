<?php

namespace Tests\Unit;

use App\Product;
use App\ProductCategory;
use App\Shop\Shop;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function smokeTest()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function smokeTestShop()
    {
        $shop = new Shop(true);

        $this->assertInstanceOf(Shop::class, $shop);
    }

    /**
     * @test
     */
    public function shouldGetCategoryWhenFactoryCategory()
    {
        /** @var ProductCategory $category */
        $category = factory(ProductCategory::class)->create();

        $target = new Shop(true);

        $actual = $target->allCategory();

        $this->assertSame($category->title, $actual[1]['title']);
    }
}
