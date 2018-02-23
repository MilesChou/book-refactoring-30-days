# 整合 CI

從開始拿到程式後，花了五天在調整程式，目的就是為了今天！

CI 的原文是 *Continuous Integration* ，也就是要「常常做整合」。如果整合能夠自動化，我們甚至也可以把任務交付給 [CI server][CI 工具大亂鬥] 處理。新的專案可以一開始就考慮自動化，但 legacy code 通常會因為自動化不完整，而讓接 CI server 不是那麼容易。

前五天最大的目的就是要讓整合的過程盡可能自動化，這樣我們才能讓 [legacy code 接 CI server][為 Legacy Code 接 CI Server] 變得比較容易一點。

---

因程式碼是放在 GitHub Public Repo ，因此決定選擇 [Travis CI](https://travis-ci.org/) 。

簡單的串接方法可以參考「[開源專案的好選擇 －－ Travis CI][]」，本文章就不多介紹，直接給 `.travis.yml` ：

```yaml
sudo: false
os: linux
dist: trusty
language: php
php: 7.1

branches:
  only:
  - example

services:
- mysql

env:
  global:
    DB_HOST: 127.0.0.1
    DB_PORT: 3306
    DB_DATABASE: shopcart
    DB_USERNAME: root
    DB_PASSWORD: password

before_install:
- mysql -e "CREATE DATABASE ${DB_DATABASE} DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;"
- mysql -e "SET PASSWORD = PASSWORD('${DB_PASSWORD}');"

install:
- composer install

before_script:
- cp .env.example .env
- php artisan key:generate
- php artisan migrate --force

script:
- php vendor/bin/phpunit

cache:
  directories:
  - vendor
```

順帶一提， Laravel 專案大概都可以用這樣的 template 作為串 Travis CI 的開始。

但 CI 這樣會回報錯誤，原因是因為下面這行出錯了：

```
$ php vendor/bin/phpunit
```

拿到本機跑也是會出錯，而這也是我們做 CI 所期望的－－CI 錯， Local 就要有一樣的錯，反之亦然。

它的錯誤大致上是因為 `config.php` 有三個不能在 PHPUnit 跑的問題：

1.  `session_start()` 不能在輸出後才執行，不過事實上 CLI 跑 session 總是很多問題，拿掉還是比較簡單的選擇
2.  `$_SERVER` 最好不要使用，不過程式並沒有使用到 `ROOT_URL` ，所以直接移除吧
3.  `define()` 被重覆定義一樣的常數，因為 PHPUnit 會重覆載入 `config.php` 導致有這樣的問題。使用 `defined` 即可順利解決：
    ```php
	defined('DB_TYPE') or define('DB_TYPE', 'mysql');
	defined('DB_CHARSET') or define('DB_CHARSET', 'utf8');
	defined('DB_HOST') or define('DB_HOST', '127.0.0.1');
	defined('DB_USER') or define('DB_USER', 'root');
	defined('DB_PASS') or define('DB_PASS', 'password');
	defined('DB_NAME') or define('DB_NAME', 'shopcart');
    ```

上面問題解決完之後，測試可以通過了，但是會把 HTML 也輸出。這是因為 `require index.php` 時，會直接把 output echo 出來，這要用 `ob_start()` 與 `ob_get_clean()` 解決：

```php
Route::get('/', function () {
    ob_start();
    require_once __DIR__ . '/../index.php';
    return ob_get_clean();
});
```

到目前為止，就可以讓 CI 正常測試了。

### 調整設定檔

現在可以比較放心的改程式了，因為 CI 會把關一部分的功能。首先設定檔有個問題是， Laravel 吃的資料庫連線設定與 config.php 的設定是不一致的，這容易發生一邊改，另一邊忘了改的問題。因此我們必須要重構調整設計。

首先 `DB_TYPE` 是多餘的，因此我們可以把它刪掉。再來，進到 config.php 時，已經可以使用 `config()` ，所以我們可以這樣取得 MySQL 的連線資訊：

```php
defined('DB_CHARSET') or define('DB_CHARSET', config('database.connections.mysql.charset'));
defined('DB_HOST') or define('DB_HOST', config('database.connections.mysql.host'));
defined('DB_USER') or define('DB_USER', config('database.connections.mysql.username'));
defined('DB_PASS') or define('DB_PASS', config('database.connections.mysql.password'));
defined('DB_NAME') or define('DB_NAME', config('database.connections.mysql.database'));
```

改完後，記得跑一下測試會不會過：

```
$ php vendor/bin/phpunit
PHPUnit 6.5.5 by Sebastian Bergmann and contributors.

....                                                                4 / 4 (100%)

Time: 220 ms, Memory: 14.00MB

OK (4 tests, 4 assertions)
```

過了，過完之後就 commit 與 push 吧！讓 CI 也測試一下。

再來 `DEBUG_MODE` 也可以使用 env 載入：

```
define('DEBUG_MODE', env('APP_DEBUG'));
```

改完一樣測試、 commit 、 push 。

### 重構的 SOP

當有了自動化測試與 CI server 之後，重構就會變得非常簡單！

上面可以看到，流程是這樣的：

1. 改程式
2. 執行測試
3. 推上 CI 驗證

測試或是 CI 驗證失敗時，我們都可以得知第一手消息，並立即修正。

對大部分的 legacy code 而言，因為通常沒有單元測試，執行測試通常很困難。但我們可以試著在最外層加上驗收測試，至少確保功能行為是正常的。

明天就來談談我們該如何追加驗收測試。

## 參考資料

* [CI 工具大亂鬥][]
* [開源專案的好選擇 －－ Travis CI][]
* [為 Legacy Code 接 CI Server][]

[CI 工具大亂鬥]: https://github.com/MilesChou/book-intro-of-ci/blob/release/docs/day22.md
[開源專案的好選擇 －－ Travis CI]: https://github.com/MilesChou/book-intro-of-ci/blob/release/docs/day24.md
[為 Legacy Code 接 CI Server]: https://github.com/MilesChou/book-intro-of-ci/blob/release/docs/day28.md
* * *
Go to next:
[day20](./day20.md)