# MVC 架構－－Controller & Model 

昨天只是單純調整程式碼風格，今天開始要來改設計了！

現在的既有程式碼是「借」住在別人的平台上，原有的程式碼都沒做調整。我們應該把程式碼調整成比較像 Laravel 設計的架構，這樣未來其他開發者來看程式碼時，只要照著 Laravel 設計去蹤縱程式碼，即可了解程式碼的細節。

## 調整 Controller

這部分應該是最好調整的， `index.php` 與 `admin.php` 內容，直接複製到 `routes/web.php` 即可。

這裡要注意的是，在引用設定檔的時候，因為路徑變了，所以需要把路徑再調整一下： 

```
// 引用設定檔
require __DIR__ . '/../config.php';
```

雖然全部的程式都集中在這個檔案，不過這是暫時的。這次重構最主要的問題是為了把外面的 `index.php` 與 `admin.php` 奇怪的設計移除。

移除後，記得要把 `phpcs.xml` 的設定也一起移除，並執行測試：

```
$ php vendor/bin/phpcs
$ php vendor/bin/phpunit
$ php artisan dusk
```

## 調整 Model

Laravel 的設計是把應用程式相關的程式放到 `app` 目錄下，因此 `mysql.class.php` 與 `shop.class.php` 是可以移到 `app` 目錄下的。

但它們都不是單純存取資料庫的 Model ，所以我們可以另外開一個目錄 `Shop` 來存放這兩隻程式。

首先先把目錄開好，取好命名空間與檔名後，並把檔案內容複製過去：

* `class/mysql.class.php` -> `app/Shop/Mysql.php`
* `class/shop.class.php` -> `app/Shop/Shop.php`

命名空間的範例如下：

```php
namespace App\Shop;

class Mysql
{
    // ...
}

class Shop
{
    // ...
}
```

PHPUnit 改讀取這個檔案：

```php
use App\Shop\Shop;

class ShopTest extends TestCase
{
    public function smokeTestShop()
    {
        $shop = new Shop(true);

        $this->assertInstanceOf(Shop::class, $shop);
    }
}
```

應用程式也要跟著調整，因為剛好同名，所以只要加 `use` 即可：

```php
// routes/web.php

use App\Shop\Shop;
```

完成後可以跑看看測試是否正常：

```
$ php vendor/bin/phpcs
$ php vendor/bin/phpunit
$ php artisan dusk
```

測試完成之後，就能移除程式了，這次移除則需要更動 `composer.json` 。記得移除完要再跑一次測試確認。

---

調整完之後，架構會越來越像 Laravel ，同時也會比較明確：第一眼看到程式會知道 `app` 是主要應用程式，而打開 `app` 目錄後，會馬上了解這個應用程式提供了一個功能叫 `Shop` 。

這也是這次重構所預期達成的效果：讓開發者能更輕鬆了解程式碼。

* * *
Go to next:
[day25](./day25.md)