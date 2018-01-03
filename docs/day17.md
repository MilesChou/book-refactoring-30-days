# 整合 Laravel

昨天[導入 Composer][] 主要是為了今天要整合 Laravel 前的準備。

今天要先初始化 Laravel 專案，再把兩個專案合併，只要處理合併後的問題即可。

以本專案為例，合併過程中，需注意幾件事：

1. `tests` 資料夾有重覆，不過這問題並不大，把本專案的範例移到 Laravel Unit 底下即可
2. `phpunit.xml` 也有重覆，不過只是 const 設定的問題。
3. 因為要使用 Laravel Router ，所以在啟動服務時，必須改用 Laravel 起服務的方法： `php artisan serve`
4. `composer.json` 需要以 Laravel 為基準合併，合併的重點是 `autoload`

最大的問題應該在 `composer.json` 的 `autoload` ，內容參考如下：

```json
"autoload": {
    "classmap": [
        "database/seeds",
        "database/factories"
    ],
    "psr-4": {
        "App\\": "app/"
    },
    "files": [
        "class/mysql.class.php",
        "class/shop.class.php",
        "workaround.php"
    ]
},
```

基本上不難，調整好就可以安裝了

```
$ composer install
```

要注意的是， Laravel 新專案剛建立時，有幾件事需要先做的：

1. 初始化 `.env` ： `cp .env.example .env`
2. 建立 APP_KEY ： `php artisan key:generate`
3. 調整 storage 權限： `chmod 777 -R storage`

到目前為止，就可以執行單元測試了：

```
$ php vendor/bin/phpunit
```

## 整合原有程式

這太容易了！把 `routes/web.php` 打開，改成這樣就行了：

```php
Route::get('/', function () {
    require_once __DIR__ . '/../index.php';
});
```

實際執行會遇到一點路徑上的問題：

```
include(/Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days/public/class/Smarty/Smarty.class.php): failed to open stream: No such file or directory
```

這是原本的 `ROOT_PATH` 寫法不是很好，可以改用 Laravel 所提供的 `base_path` 函式：

```php
define('CLASS_PATH', base_path('/class/'));

include(CLASS_PATH . "Smarty/Smarty.class.php");
$tpl = new Smarty;
$tpl->template_dir = base_path('/templates/');
$tpl->compile_dir = base_path('/templates/compile/');
$tpl->config_dir = base_path('/templates/configs/');
$tpl->cache_dir = base_path('/templates/cache/');
```

最後把 `index.php` 裡的 `include` 改成 `require_once` ，不然載入的時候會遇到重覆定義的錯誤訊息：

```
Cannot declare class db, because the name is already in use
```

處理完這個錯誤，接著會發現一個使用 Smarty 的慘案：

```
The each() function is deprecated. This message will be suppressed on further calls
```

PHP 7.2 不支援 `each()` function ，所以只能降級使用 PHP 7.1 開發。

解決完就能看到 HTML 了，但沒有 css/js ，因為都放在主目錄，移到 public 目錄下即可。

管理頁面做法一樣，先加 route ：

```php
Route::get('/admin', function () {
    require_once __DIR__ . '/../admin.php';
});
```

然後把載入改成 `require_once` 就沒問題了。

---

到目前為止，使用 Laravel 啟動服務就能看到跟原本首頁一樣的畫面了：

```
$ php artisan serve
```

程式碼可以參考 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/4)

[導入 Composer]: /docs/day16.md
