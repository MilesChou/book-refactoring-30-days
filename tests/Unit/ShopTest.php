<?php

namespace Tests\Unit;

use App\ategory;
use App\Product;
use App\ProductCategory;
use App\Shop\Shop;
use Tests\TestCase;

class ShopTest extends TestCase
{
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
        $target = $this->app->make(Shop::class);

        $this->assertInstanceOf(Shop::class, $target);
    }

    /**
     * @test
     */
    public function shouldGetAllCategoryWhenSeedFactoryCategory()
    {
        /** @var ProductCategory $excepted */
        $excepted = factory(ProductCategory::class)->create();

        $target = $this->app->make(Shop::class);

        $actual = $target->allCategory();

        $this->assertSame($excepted->id, $actual[1]['id']);
        $this->assertSame($excepted->title, $actual[1]['title']);
    }

    /**
     * @test
     */
    public function shouldGetOneCategoryWhenSeedFactoryCategory()
    {
        /** @var ProductCategory $excepted */
        $excepted = factory(ProductCategory::class)->create();

        $target = $this->app->make(Shop::class);

        $actual = $target->oneCategory($excepted->id);

        $this->assertSame($excepted->id, $actual['id']);
        $this->assertSame($excepted->title, $actual['title']);
    }

    /**
     * @test
     */
    public function shouldGetOneWhenSeedFactoryCategory()
    {
        /** @var Product $excepted */
        $excepted = factory(Product::class)->create();
        $exceptedClick = $excepted->click + 1;

        $target = $this->app->make(Shop::class);

        $actual = $target->one($excepted->id);

        $this->assertEquals($excepted->id, $actual['id']);
        $this->assertEquals($excepted->price, $actual['price']);
        $this->assertSame($excepted->title, $actual['title']);
        $this->assertSame($excepted->content, $actual['content']);

        // 確認 click 會加 1
        $this->assertSame($exceptedClick, $excepted->refresh()->click);
    }
}
