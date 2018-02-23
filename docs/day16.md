# 導入 Composer

[Composer][] 是目前 PHP 界裡廣泛使用的套件管理工具，用 Composer 的自動載入也會比較容易跟 Laravel 串接。我們今天就來導入 Composer 吧！

首先先下載 Composer 指令：

```
# Linux
# 產生 composer.phar，然後可以直接執行操作
$ curl -sS https://getcomposer.org/installer | php
# 用全域的方式執行就再下這個指令
$ sudo mv composer.phar /usr/local/bin/composer

# Mac 請用 Homebrew 安裝比較保險，用上面的方法安裝會遇到找不到 extension 的怪問題
$ brew install composer
```

> 後面會用全域的方式當範例

確認有下載成功：

```
$ composer --version
Composer version 1.5.5 2017-12-01 14:42:57
```

接著可以進到目錄下初始化指令：

```
$ composer init
```

照提示一步一步輸入資訊即可，依賴套件先都不要安裝。

或是直接使用下面的 `composer.json` 檔

```json
{
    "require": {}
}
```

另外 `vender` 資料夾也需要加入 `.gitignore` ：

```
# Composer file
/vendor/
```

## 安裝第一個套件

只把 Composer 準備好，不安裝個套件好像說不過去。

我們今天也來安裝 PHPUnit ，順便把 Autoload 設定好。

首先安裝 PHPUnit

```
$ composer require phpunit/phpunit
```

接著初始化 PHPUnit 的設定檔： `phpunit.xml.dist`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit backupGlobals="false"
         backupStaticAttributes="false"
         bootstrap="vendor/autoload.php"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         processIsolation="false"
         stopOnFailure="false">
    <testsuites>
        <testsuite name="UnitTest">
            <directory suffix="Test.php">./tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

這個設定檔會找 `tests` 資料夾裡， `Test.php` 結尾的檔案來當 TestCase 執行。建好資料夾後，我們來寫一個最簡單的測試：

```php
<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * @test
     */
    public function smokeTest()
    {
        $this->assertTrue(true);
    }
}
```

接著執行：

```
$ php vendor/bin/phpunit
PHPUnit 6.5.5 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 47 ms, Memory: 4.00MB

OK (1 test, 1 assertion)
```

這樣就代表測試正常。 TestCase 裡面，一個測試會是一個 method ，我們再來加一個新的測試：

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

它會說測試失敗，因為 `shop` 找不到。我們必須要加入自動載入機制，讓 PHPUnit 能找得到它，最簡單的方法是使用 autoload 的 file 方法：

```json
{
    "autoload": {
        "files": [
            "class/mysql.class.php",
            "class/shop.class.php",
            "workaround.php"
        ]
    }
 }
```

執行完 `composer dump-autoload` 後，出現另一個錯誤訊息了：

```
Use of undefined constant DB_HOST - assumed 'DB_HOST' (this will throw an Error in a future version of PHP)

/Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days/class/mysql.class.php:32
/Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days/class/shop.class.php:56
/Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days/tests/ExampleTest.php:23
```

因為常數在跑 PHPUnit 一開始沒設定，建議這一類的環境變數，一開始先不要載入 `config.php` ，而是在 XML 裡設定：

> [文件參考](https://phpunit.de/manual/current/zh_cn/appendixes.configuration.html#appendixes.configuration.php-ini-constants-variables)

```xml
<php>
    <const name="DB_HOST" value="127.0.0.1"/>
    <const name="DB_USER" value="root"/>
    <const name="DB_PASS" value="password"/>
    <const name="DB_NAME" value="shopcart"/>
    <const name="DB_CHARSET" value="utf8"/>
</php>
```

這裡要注意的是，因為這次執行單元測試不是在容器裡，所以 `DB_HOST` 要改為本機， Docker Container 也要注意是否有把 port 打開。 

最後再跑一次就會成功了：

```
php vendor/bin/phpunit 
PHPUnit 6.5.5 by Sebastian Bergmann and contributors.

..                                                                  2 / 2 (100%)

Time: 72 ms, Memory: 4.00MB

OK (2 tests, 2 assertions)
```

從這刻起，不一定要靠 UI 才能做測試，也可以開始使用 PHPUnit 來測試元件囉！

今天的程式碼修改可以參考 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/3)

[Composer]: https://getcomposer.org/

* * *
Go to next:
[day17](./day17.md)