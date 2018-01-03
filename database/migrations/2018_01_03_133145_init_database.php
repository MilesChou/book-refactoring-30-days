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
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->text('data');
            $table->integer('total');
            $table->string('sn')->unique();
            $table->boolean('_checkout');
        });

        Schema::create('product', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category');
            $table->string('title');
            $table->text('content');
            $table->string('pic');
            $table->integer('cost');
            $table->integer('price');
            $table->integer('store');
            $table->integer('sale');
            $table->integer('click');
        });

        Schema::create('product_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
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
