<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Shop;

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
}
