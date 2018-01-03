<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use shop;

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
        $shop = new shop(true);

        $this->assertInstanceOf(shop::class, $shop);
    }
}
