# 導入驗收測試（1）

導入 Composer 的時候，我們有新增一個範例的單元測試。如果可以的話，下一步當然就是開始寫一些基本的單元測試，來保護系統元件。但，並不是每個專案都能這麼開心，如果真的無法寫單元測試的話該怎麼辦呢？沒關係，我們還能寫驗收測試！

## HTTP Tests

Laravel 的測試套件相當完整， [HTTP Tests](https://laravel.com/docs/5.5/http-tests) 是 Laravel 測 HTML 的說明文件。

我們先試著修改 `tests/Feature/ExampleTest.php` ，寫看看首頁的測試：

```php
class ExampleTest extends TestCase
{
    /**
     * @test
     */
    public function indexPage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('產品分類');
        $response->assertSee('管理員頁面');
        $response->assertSee('聯絡我們');
        $response->assertSee('查看購物車');
        $response->assertSee('回首頁');
    }
}
```

接著跑測試：

```
$ php vendor/bin/phpunit
```

這裡大家可以試著做一件事：把 Server 關掉，再跑一次測試看看。

大家會發現，其實測試還是能正常執行的。這是因為 Laravel 把輸出頁面的方法，包成了物件，直到 route 處理到某個階段的時候，才輸出網頁； Laravel 的測試則是在輸出到網頁前，把物件擋下來然後拿來當字串（HTML）驗證。

這個做法好處當然很多，最大的好處是：只要能跑 PHP ，不需要起伺服器就能執行測試。這執行測試的機會變得更大（因為需要的條件變少了）；同樣的，不起伺服器，就少了非常多傳輸成本，速度肯定是更快的。

有一好沒兩好，它的壞處就是無法完全模擬真實的環境，包括 HTTP Server 或是瀏覽器執行 Javascript 等。除此之外，因為會使用一個 process 執行所有測試，這代表所有測試的記憶體是共享的，那就得小心**單例模式**全域影響的問題或是**自動載入**的問題等。

首頁測好了，那我們來測看看管理頁：

```php
/**
 * @test
 */
public function shouldBeOkWhenSeeAdminPage()
{
    $response = $this->get('/admin.php');

    $response->assertStatus(200);
    $response->assertSee('管理功能');
    $response->assertSee('統計功能');
}
```

但它會發生錯誤：

```
1) Tests\Feature\ExampleTest::shouldBeOkWhenSeeAdminPage
Expected status code 200 but received 404.
```

它說「預期會拿到 status code 200 但事實上拿到了 404 」，這很有可能是因為我們 Route 並沒有設定好，來調整一下 `routes/web.php` ：

```php
// 忘了在後面加 .php
Route::get('/admin.php', function () {
    ob_start();
    require_once __DIR__ . '/../admin.php';
    return ob_get_clean();
});
```

這裡調整完後，會發生很杯具的錯誤：

```
1) Tests\Feature\ExampleTest::shouldBeOkWhenSeeAdminPage
Expected status code 200 but received 500.
```

它說「預期會拿到 status code 200 但事實上拿到了 404 」，查了一下 log ，它說找不到 tpl 這個變數：

```
Undefined variable: tpl at /Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days/admin.php:194
```

往源頭找 `$tpl` 是在 `config.php` 設定的，找不到代表沒有載入過。這正是前面有提到的「自動載入」問題，因為 `config.php` 曾被 require 過，所以這裡就不會再 require 了，才會找不到變數。最簡單的解法是：把 `require_once` 改成 `require` ，不過十之八九都有可能出事，就先試看看。

改完跑完之後，出現別的錯誤了：

```
DEBUG_MODE already defined at /Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days/config.php:14
```

這昨天有提到解法如下：

```
defined('DEBUG_MODE') or define('DEBUG_MODE', env('APP_DEBUG'));
```

後面也有許多類似的錯誤都一起解掉，再跑一次應該就會過了。

---

今天成功把基本的驗收測試加上去了，明天我們再換跑瀏覽器的測試。

程式碼可以參考 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/7)
