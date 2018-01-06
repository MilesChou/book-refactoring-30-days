# 準備工作的簡短回顧（1）

在拿到程式碼的時候，我們並不是立刻把程式碼砍掉重練，而是先做一連串的準備工作：

* [Day 14][] 重構的第一步－－讓程式可以動
* [Day 15][] 來試著升級 PHP 吧
* [Day 16][] 導入 Composer
* [Day 17][] 整合 Laravel
* [Day 18][] 導入 Database Migration
* [Day 19][] 整合 CI
* [Day 20][], [Day 21][] 導入驗收測試

每個階段其實都有一些重構的影子在裡面，我們先來看前三個階段所做的事。

## 先讓程式可以跑

開發重要的任務之一是做*功能測試*。因為程式執行的[結果要正確][先求有，再求好？]，才能產生預期的價值。

而我們拿到一份既有程式碼（legacy code）後，首要任務應該是要了解*如何執行*，才能進一步做功能測試。

### 設定檔的壞味道

這個階段是為了確認既有程式碼是可以運行的，以及確認修改程式前的行為。因此這天應該只能建構環境，不能修改程式。

但相信大家會發現，這天其實是有修改程式碼（`config.php`）的：

```php
define('DB_HOST', 'mysql');
define('DB_USER', 'root');
define('DB_PASS', 'password');
```

或許有人會認為這是設定檔，但事實上，這個檔案會被 `index.php` 與 `admin.php` 引用，因此只要 `config.php` 有任何問題，都有可能造成系統錯誤。

最好的方法，就是不要改它。資料庫是環境的依賴，因此從環境變數來取得對應的設定，會是更好的設計：

```php
define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
```

Laravel 則可以使用 `env` 或 `config` 函式，來取得 `.env` 或 config 目錄下的設定值了：

```php
// 使用 env
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', 'password'));

// 使用 config
define('DB_HOST', config('database.connections.mysql.host'));
define('DB_USER', config('database.connections.mysql.username'));
define('DB_PASS', config('database.connections.mysql.password'));
```

## 升級 PHP

升級 PHP 最主要的目的是為了要使用 [Laravel](https://laravel.com/) 框架的測試套件。以 Laravel 5.5 來說，它最低需求是 PHP 7 ，所以目標才會放在升級 PHP 7 上。

除此之外，最重要的目的是為了 [Built-in web server](http://php.net/manual/en/features.commandline.webserver.php) 。

雖然它的行為與 Apache 有些許的不同，但大部分的功能都是一樣的。有了它，將可以減少許多建置測試環境的問題，讓時間都盡可能花在改善設計。

## 使用 Composer 自動載入

Composer 除了提供了第三方套件管理功能之外，也提供了自動載入機制。

必須要能在 Laravel 裡正常載入既有程式碼，才能使用 Laravel 當做 proxy 。 Composer 可以實作自動載入，讓 Laravel 可以正常載入。

實作自動載入後，同時也安裝 PHPUnit 做簡單測試，以確認 Composer 的自動載入是有效的：

```php
/**
 * @test
 */
public function smokeTestShop()
{
    $shop = new shop(true);
    $this->assertInstanceOf(shop::class, $shop);
}
```

事實上，只要單元測試有確定 Composer 自動載入正常的話，載入 class 的方法就可以移除了：

```php
require_once CLASS_PATH . 'Smarty/Smarty.class.php';
require_once CLASS_PATH . 'mysql.class.php';
require_once CLASS_PATH . 'shop.class.php';
```

移除之後，載入方法調整就不影響原始碼了，這也能讓原始碼能更專注在處理商業邏輯上，如同[單一職責原則][Day 7]所想達成的目的一樣－－只有商業邏輯調整才會改變原始碼。

我們現在也可以試著修改看看，只要測試的結果是正確的，代表我們的修改是不影響正常行為的。

1. 新增 Autoload 檔案： `class/Smarty/Smarty.class.php`
2. 重新載入 Autoload `composer dump-autoload`
3. 移除 `config.php` `index.php` `admin.php` 裡相關的引用

完成後再跑測試：

```
$ php vendor/bin/phpunit
$ php artisan dusk
```

一切正常就代表我們的修正是正確的了！

相關程式碼可以參考 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/10)

## 參考資料

* [先求有，再求好？][]

[先求有，再求好？]: https://github.com/MilesChou/book-intro-of-ci/blob/release/docs/day04.md
[Day 7]: /docs/day07.md
[Day 14]: /docs/day14.md
[Day 15]: /docs/day15.md
[Day 16]: /docs/day16.md
[Day 17]: /docs/day17.md
[Day 18]: /docs/day18.md
[Day 19]: /docs/day19.md
[Day 20]: /docs/day20.md
[Day 21]: /docs/day21.md
