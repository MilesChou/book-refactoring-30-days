# 整合 Eloquent

今天準備要來把 Eloquent 整合進程式裡，但因為底層的資料庫範圍這麼大，我們該如何知道測試有沒有跑到改的地方呢？

這時 Coverage 就是一個很好的幫手了，我們將會串接 *codecov* 服務來協助重構。

## 串接 codecov

[codecov][] 是一個 SaaS 服務，一般我們版本控制只會重視在程式碼上，而 codecov 會幫你把 coverage 的結果也版控，並且在 coverage 有風險的時候給予提醒。

首先先上官網使用 GitHub 登入，接著使用下面這個 pattern 進入自己的專案：

```
https://codecov.io/gh/<username>/<repo

如

https://codecov.io/gh/MilesChou/book-refactoring-30-days
``` 

接著裡面是很簡單的 step by step ，照著做即可。簡單來說，拿到 token 然後設定在 CI 上即可。官網已有很多[範例程式](https://docs.codecov.io/docs/supported-languages)，這邊就不贅述了。

`.travis.yml` 可以參考[整合的 PR](https://github.com/MilesChou/book-refactoring-30-days/pull/15)

## 整合程式

參考 Coverage 的[資料](https://codecov.io/gh/MilesChou/book-refactoring-30-days/src/6e1f189ed73f2a67b307147098c67c1c0cae845d/app/Shop/Shop.php#L260...275)，我們可以先改這兩個 function ，因為測試會測到這邊。

```php
public function allCategory(): Collection
{
    return ProductCategory::all();
}

public function oneCategory($id): ProductCategory
{
    return ProductCategory::find($id);
}
```

測試的結果當然是一帆風順，但我們會發現 [Coverage 結果](https://codecov.io/gh/MilesChou/book-refactoring-30-days/pull/16/changes)並不一定是這樣， codecov 厲害的地方在於，它會在 PR 的時候，提交報告訊息到該 PR 上，讓人不想注意都不行：

![codecov-report][]

上面的訊息我們可以看到，它說 coverage 下降了 1.64% 。一般來說， coverage 下降，大部分都不是什麼好事，但這次的狀況剛好比較特別。

首先我們修改的兩個方法，都從兩行變一行，因為 PHP coverage 目前只支援 C1 ，所以 `Shop.php` 下降是正常的。甚至我們會希望它會下降－－因為程式碼變得更簡單了。

而 `Mysql.php` 情況很類似，我們應該都會希望使用 Laravel 的 Eloquent 或 SQL Builder ，而不是用這隻自幹又沒有測試的 SQL Builder ，所以也會希望它的 coverage 是下降的。

而我們則會希望商業邏輯的 coverage 是上升的，因為那才是產品的命脈。

## 重構更多方法

結合昨天與今天的方法，我們可以把更多底層的方法都重構成 Eloquent ，如 `one()` 的測試：

```php
/**
 * @test
 */
public function shouldGetOneWhenSeedFactoryCategory()
{
    /** @var Product $excepted */
    $excepted = factory(Product::class)->create();
    $exceptedClick = $excepted->click + 1;

    $target = new Shop(true);

    $actual = $target->one($excepted->id);

    $this->assertEquals($excepted->id, $actual['id']);
    $this->assertEquals($excepted->price, $actual['price']);
    $this->assertSame($excepted->title, $actual['title']);
    $this->assertSame($excepted->content, $actual['content']);

    // 確認 click 會加 1
    $this->assertSame($exceptedClick, $excepted->refresh()->click);
}
```

> 測試同時也發現 `workaround.php` 有 bug 。

一樣在 [coverage](https://codecov.io/gh/MilesChou/book-refactoring-30-days/pull/16/changes) 看到有涵蓋到之後，再開始重構：

```php
public function one($id): Product
{
    /** @var Product $product */
    $product = Product::find($id);
    $product->click++;
    $product->save();

    return $product;
}
```

這樣就不僅僅是測試通過，涵蓋的範圍也在預期之內。

## 把 Controller 也加進去

這裡的 Coverage 並不包括 `routes/web.php` ，因為 PHPUnit 設定並沒有包含它。所以我們可以改設定或是改程式，來看到更悲劇的 coverage 。

我們將使用改程式的方法達成，這也是解決 [Day 24][] 提到的問題：所有程式碼都擠在 route 裡，並不是一個好做法。

首先先新增 Controller ：

```
$ php artisan make:controller AdminController
$ php artisan make:controller ShopController
```

然後把對應的程式碼塞到對應的 Controller 即可：

```php
class AdminController extends Controller
{
    public function index(Request $request)
    {
        // ...
    }
}

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // ...
    }
}
```

route 這時就會改比較精簡：

```php
Route::get('/admin.php', 'AdminController@index');
Route::get('/', 'ShopController@index');
```

但 coverage 就會非常悲劇了，因為最複雜的邏輯都在 controller 裡。

---

今天的程式碼範例在 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/16) 裡，明天我們來想辦法解決 Controller 的問題。

## 參考資料

* [codecov][]

[codecov]: https://codecov.io/
 
[Day 24]: /docs/day24.md 
[codecov-report]: /images/codecov-report.png
