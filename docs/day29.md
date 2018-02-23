# 組合應用

之前都是柿子挑軟的吃，專找簡單的好重構的目標下手。今天我們就來找個比較難搞的目標 Product 功能來試看看重構吧！

## 寫測試

第一步當然要先寫測試，以下是個簡單的測試：

```php
public function testAddProductAndSeeProduct()
{
    /** @var Generator $faker */
    $faker = $this->app->make(Generator::class);

    $this->browse(function (Browser $browser) use ($faker) {
        $image = $faker->image(base_path('storage/app/public'), 640, 480, 'cats');

        $title = '野貓';
        $content = '一天要吃十個罐罐！一天要吃十個罐罐！一天要吃十個罐罐！很重要要說三次';

        $browser->visit('/admin.php?act=shop&op=view')
            ->assertSee('標題')
            ->assertSee('分類')
            ->assertSee('成本')
            ->type('title', $title)
            ->type('cost', 100)
            ->type('price', 200)
            ->type('store', 10)
            ->attach('pic', $image)
            ->type('content', $content)
            ->click('.mainFrameHelp > div > input[type="submit"]');

        $browser->driver->switchTo()->alert()->accept();

        $browser->assertSee($title)
            ->assertSee($content)
            ->stop();
    });
}
```

這裡使用 Faker 建 image 與 Dust 上傳檔案的功能來做測試。

接著來處理樣版，後來才發現 `admin.html` 其實是子樣版也是其他 admin 頁面的主樣版，所以必須來處理一下：

* `admin.html` -> `admin/index.blade.php`
* `admin_shop.html` -> `admin/product.blade.php`

在程式裡載入 Blade 樣版檔成功後，調整樣版檔讓原本的測試通過。後面可以加測試檢查 Smarty Tags 是否有忘了沒改的：

```php
public function testShouldNotSeeSmartyTagAtAdminProductPage()
{
    $this->browse(function (Browser $browser) use ($faker) {
        $browser->visit('/admin.php?act=shop&op=view')
            ->assertDontSee('<%')
            ->assertDontSee('%>');
    });
}
```

再來開始切 Controller ，先把 `op=view` 複製出去，除了 controller 和 route 要調整外，測試也需要調整：

```php
// controllers
class ProductController extends Controller
{
    public function main(Request $request, Shop $shop)
    {
        $id = $request->query('id');

        return view('admin.product', [
            'one' => $shop->one($id),
            'all' => $shop->all(),
            'all_category' => $shop->allCategory(),
        ]);
    }
}

// routes
Route::get('/admin/product', 'Admin\ProductController@main');

// tests
$browser->visit('/admin/product');
```

接著，因為剛剛 `/admin.php?act=shop&op=view` 這個連接是可用的，我們可以用這個字串來找到程式有哪裡連結到這頁，結果應該只有在後台出現：

```html
<a href="/admin.php?act=shop&amp;op=view">商品管理</a>
```

還有 PHP 程式裡處理的 showAlert 

```php
$shop->showAlert('商品已新增', 'admin.php?act=shop&op=view')
```

接著，補測試到一開始的 `testAddProductAndSeeProduct` 測試案例，我們改由首頁點過去：

```php
$browser->visit('/admin')
    ->assertSee('商品管理')
    ->clickLink('商品管理')

// 新增完驗連線
$browser->assertPathIs('/admin.php');
```

這兩個加上去是會對的，接著我們把原本的實作拔掉，測試就會壞掉了，然後我們再想辦法修好它即可。

---

剩下的太多了，就不繼續做了。

如此做完的話，功能可靠度就會非常高，因為我們可以注意到，每次都會用測試來確認功能被改壞或是修好。如果有被改壞再修好的過程，開發者將可以非常確定，現在重構好的程式是正常運行的。

程式碼可以參考 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/18)

* * *
Go to next:
[day30](./day30.md)