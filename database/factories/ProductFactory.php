<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Product::class, function (Faker $faker) {
    return [
        'category' => function () {
            return factory(App\ProductCategory::class)->create()->id;
        },
        'title' => $faker->word,
        'content' => $faker->text,
        'pic' => '',
        'cost' => $faker->randomElement([100, 200, 300]),
        'price' => $faker->randomElement([400, 500, 600]),
        'store' => $faker->randomElement([10, 20, 30]),
        'sale' => $faker->randomElement([10, 20, 30]),
        'click' => 0,
    ];
});

$factory->define(App\ProductCategory::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
    ];
});

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        'datetime' => $faker->dateTime,
        'name' => $faker->name,
        'email' => $faker->email,
        'phone' => $faker->numerify('09########'),
        'address' => $faker->address,
        'data' => '{}',
        'total' => 0,
        'sn' => str_replace('-', '', $faker->uuid),
        '_checkout' => 0,
    ];
});
