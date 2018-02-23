# 導入驗收測試（2）

今天要寫瀏覽器的驗收測試。

跟昨天不一樣的地方在於，現在要測的是瀏覽器的行為，我們必須要*啟動伺服器*，並*佈署程式*，才能*開瀏覽器*測試。

這樣的好處是：過程更像上線，中間哪裡有環節出錯，甚至是測試過程出錯，代表上線人工做一樣的測試，極有可能會發生一樣的問題。但壞處就是執行的時間成本太高，跑上千個單元測試，可能還比跑一個驗收測試快；錯誤回饋的時間會拉很長，所以除錯時間也會跟著拉長。

但不管怎麼說，有需要開瀏覽器測功能的話，寫成自動化肯定能帶來很大的效益。


## Browser Tests

Laravel 也有提供強大的瀏覽器測試套件， [Dusk](https://laravel.com/docs/5.5/dusk) 。今天就來實作 Dusk 的測試吧！

首先先安裝套件：

```
$ composer require laravel/dusk
```

套件安裝完後，再初始化 Dusk ：

```
$ php artisan dusk:install
```

`tests/Browser/ExampleTest.php` 是它預設的範例測試。

我們可以先來測試看看，不過這次測試得先做點準備工夫，首先要調整環境設定 `.env` ：

```
APP_URL=http://localhost:8000
```

接著啟動伺服器：

```
$ php artisan dusk
```

現在就能開始跑測試了：

```
$ php artisan dusk

There was 1 failure:

1) Tests\Browser\ExampleTest::testBasicExample
Did not see expected text [Laravel] within element [body].
Failed asserting that false is true.
```

什麼！居然出錯了？

這是因為 Dusk 預設是測官方的預設頁面，裡面有大大的 `Laravel` ，現在官方的預設頁面被我們換成首頁了，所以我們需要修改測試檔 `tests/Browser/ExampleTest.php` ：

```php
<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ExampleTest extends DuskTestCase
{
    /**
     * @test
     */
    public function shouldBeOkWhenSeeIndexPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('產品分類')
                ->assertSee('管理員頁面')
                ->assertSee('聯絡我們')
                ->assertSee('查看購物車')
                ->assertSee('回首頁')
                ->clickLink('管理員頁面')
                ->assertSee('統計功能')
                ->clickLink('查看購物車')
                ->assertSee('購物清單');
        });
    }

    /**
     * @test
     */
    public function shouldBeOkWhenSeeAdminPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin.php')
                ->assertSee('管理功能')
                ->assertSee('統計功能')
                ->clickLink('商品管理')
                ->assertSee('庫存')
                ->clickLink('訂單管理')
                ->assertSee('訂單列表');
        });
    }
}
```

Laravel 提供的測試方法很語意化，可以了解這個測試的方法為何。修改完後，測試就會正常了：

```
$ php artisan dusk
PHPUnit 6.5.5 by Sebastian Bergmann and contributors.

..                                                                  2 / 2 (100%)

Time: 1.93 seconds, Memory: 14.00MB

OK (2 tests, 11 assertions)
```

## 整合 CI

最後，我們會希望 CI 把關一切測試，當然也包括了瀏覽器測試。

官方有提供如何串接 CI 的[範例](https://laravel.com/docs/5.5/dusk#continuous-integration)，裡面重要的關鍵有下面幾個：

1.  要安裝 Chrome ，因此需要 sudo 權限
    ```
    sudo: required
    addons:
       chrome: stable
    ```

2.  背景啟動 Chrome Headless 模式與 Server
    ```
    before_script:
    - google-chrome-stable --headless --disable-gpu --remote-debugging-port=9222 http://localhost &
    - php artisan serve &
    ```

3.  URL 設定
    ```
    env:
      global:
        APP_URL: http://localhost:8000
    ```

4.  執行測試
    ```
    script:
    - php artisan dusk
    ```

這樣 Travis CI 也就可以幫我們測試 UI 了。

---

到目前為止，測試的初始化已經算完成最簡單部分了。有了這些測試，後面的重構將會因此更為順利。

相關的程式碼可以參考 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/8)

* * *
Go to next:
[day22](./day22.md)