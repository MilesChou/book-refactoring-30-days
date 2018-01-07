<?php

namespace Tests\Unit;

use App\ategory;
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
        $shop = new Shop(true);

        $this->assertInstanceOf(Shop::class, $shop);
    }

    /**
     * @test
     */
    public function shouldGetAllCategoryWhenSeedFactoryCategory()
    {
        /** @var ProductCategory $excepted */
        $excepted = factory(ProductCategory::class)->create();

        $target = new Shop(true);

        $actual = $target->allCategory();

        // FIXME: 因型態不同，先使用 equals
        $this->assertEquals($excepted->id, $actual[1]['id']);
        $this->assertSame($excepted->title, $actual[1]['title']);
    }

    /**
     * @test
     */
    public function shouldGetOneCategoryWhenSeedFactoryCategory()
    {
        /** @var ProductCategory $excepted */
        $excepted = factory(ProductCategory::class)->create();

        $target = new Shop(true);

        $actual = $target->oneCategory($excepted->id);

        // FIXME: 因型態不同，先使用 equals
        $this->assertEquals($excepted->id, $actual['id']);
        $this->assertSame($excepted->title, $actual['title']);
    }
}
