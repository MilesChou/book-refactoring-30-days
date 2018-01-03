<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitDatabase extends Migration
{
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('datetime');
            $table->string('name', 30);
            $table->string('email', 30);
            $table->string('phone', 15);
            $table->string('address', 100);
            $table->text('data');
            $table->integer('total');
            $table->string('sn', 32)->unique();
            $table->boolean('_checkout');
        });

        Schema::create('product', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category');
            $table->string('title', 30);
            $table->text('content');
            $table->string('pic', 50);
            $table->integer('cost');
            $table->integer('price');
            $table->integer('store');
            $table->integer('sale');
            $table->integer('click');
        });

        Schema::create('product_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 30);
        });

        // TODO: 未來移至 Seeder
        DB::table('product_category', [
            'id' => 0,
            'title' => '未分類',
        ]);
    }

    public function down()
    {
        Schema::drop('order');
        Schema::drop('product');
        Schema::drop('product_category');
    }
}
