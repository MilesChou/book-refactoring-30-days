# 導入 Database Migration

重構的過程中，最重要卻也最麻煩的流程，就是**驗證**。我們必須確保重構的過程不會把原本的功能改壞，只能靠不斷的測試，驗證功能沒壞，才能繼續下一步。

目前程式最讓我們頭痛的，應該就是資料庫初始化了，因為它必須要手動進入 Docker Container 匯入資料，才能把測試環境建立起來。

上班都有會忘記打卡了，人為固定要執行的操作行為，都有可能失誤，資料庫就要交給專業的 Migration 來處理了。

## 開工

Laravel 已經有預先幫我們建兩個 migration ，我們先把它們都刪掉，然後使用指令建立一個新的 migration 

```
$ php artisan make:migration init_database
Created Migration: 2018_01_03_133145_init_database
```

接著我們要把 SQL 轉成 [Laravel Migration](https://laravel.com/docs/5.5/migrations) 可以接受的寫法：

```php
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
```

轉換成這樣會有一個好處，現在先賣個關子。

因為 migration 不會建 database ，因此建立 Docker Container 的指令要調整：

```
$ docker run -d -e MYSQL_ROOT_PASSWORD=password -e MYSQL_DATABASE=shopcart -p 3306:3306 -v `pwd`:/source --name some-mysql mysql:5.6
```

這樣就會有預設的資料庫了。

跑一次試看看，理論上會出現下面這個錯誤：

```
$ php artisan migrate
SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long; max key length is 767 bytes
```

可以參考[這個網頁](https://laravel-news.com/laravel-5-4-key-too-long-error)解決，我們直接加到 `AppServiceProvider` 吧：

```php
use Illuminate\Support\Facades\Schema;

public function boot()
{
    Schema::defaultStringLength(191);
}
```

再跑一次應該就會正常了：

```
$ php artisan migrate
```

來試看看起 server 會不會正常執行：

```
$ php artisan serve
```

記得也試一下 rollback 會不會正常執行：

```
$ php artisan migrate:rollback
```

今天這樣改完之後，後面維護程式碼就能少做很多事，也減少錯誤發生的機會。

## 該怎麼幫正在飛的飛機換引擎？

維運中的產品必須考慮線上的可用性，因此不能隨便調整 schema 。因此我們應當要調整 migration ，讓程式產出的 schema 盡可能跟線上相容，才不會發生奇妙的 bug 一直找不到。

現在已經有自動化建 SQL 的方法了，再來我們只要能輸出現有的 schema 即可，我們可以下這個指令得知目前的 schema ：

```
$ docker exec -it some-mysql mysqldump -u root -ppassword shopcart
...
```

跟原本 SQL 比較，會發現主要都是長度不一致，這時要思考一些問題：哪些是必要要調整的？如： `int` 長度問題並不大，但 `varchar` 長度比線上的大，就有可能造成測試正常，但線上出現文字被截斷的問題。

至於 `ENGINE` 跟編碼筆者就不確定了，但本專案單純只是 CRUD ，這兩個設定不一樣應該不會有什麼問題。

這裡的調整就不列出來，程式碼詳細可以參考 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/5)

* * *
Go to next:
[day19](./day19.md)