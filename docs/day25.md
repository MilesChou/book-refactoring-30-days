# MVC 架構－－View

今天的任務有點麻煩，我們要把 [Smarty][] 轉換成 [Blade][] 。

## 理解舊樣版設計

Controller 只有兩個主要檔案，搬移相對簡單很多；樣版則有 9 個檔案，自然會比較複雜。

舊樣版設計是這樣的：

* `admin.html` - 管理員頁面的子樣版
* `admin_login.html` - 管理員登入頁的子樣版
* `admin_order.html` - 管理員的訂單管理頁的子樣版
* `admin_orderInfo.html` - 管理員的訂單管理細節子頁的子樣版
* `admin_shop.html` - 管理員的商品管理頁的子樣版
* `contact.html` - 聯絡我們的子樣版
* `index.html` - 主樣版
* `shop.html` - 首頁的子樣版
* `shop_cart.html` - 查看購物車的子樣版

`index.html` 是唯一的主樣版，其他都依附在這個主樣板之下。因此我們只要成功把一組主樣版與子樣版移植成功的話，其他都不會有問題了。

順帶一提，上面這樣的命名方法會造成一些誤會，如 `admin.html` 和 `index.html` 會誤以為是 `admin.php` 與 `index.php` 的首頁。

比較好的方法是把 `index.html` 改名為 `layout.html` 之類的命名。 

## 初始化樣版目錄

Laravel 的 View 放在 `resources/views` 裡，我們建三個目錄：

* `layouts` 放主樣版
* `admin` 放後台的樣版
* `shop` 放前台的樣版

首先我們先拿主樣版 `index.html` 已經有測試的首頁 `shop.html` 來開刀。先把它們複製到對應的目錄：

* `templates/index.html` -> `resources/views/layouts/main.blade.php`
* `templates/shop.html` -> `resources/views/shop/index.blade.php`

然後在 index route 裡面的 main case 加入一點手腳，就能看得到結果了：

```php
case 'main':
default:
    ob_get_clean();

    return view('shop.index');

    // 原本的程式碼 ...
```

當然會看到一堆亂碼，我們先跑測試看看，至少先確定它是正常回傳 `200` ：

```
$ php vendor/bin/phpunit
1) Tests\Feature\ExampleTest::shouldBeOkWhenSeeIndexPage
Failed asserting that '...' contains "管理員頁面".
```

看起來只是找不到主樣版的內容，那不管怎麼說，我們先讓這個測試過關，先在子頁面實作 `@extend` 。

```
<!-- shop/index.blade.php -->
@extends('layouts.main')
```

接著測試就會過了！？是的，因為測試只認關鍵字在不在，因此樣版只要有輸出，測試就會正常。

但相反地，我們也可以驗證某些關鍵字不存在，如亂碼。這些亂碼是有 pattern 的，因為 Smarty 樣版可以自定義標籤，此專案定義如下：

```php
$tpl->left_delimiter = '<%';
$tpl->right_delimiter = '%>';
```

在找程式碼時，可以使用正則 `<%.*%>` 。測試則可以這樣寫：

```php
/**
 * @test
 */
public function shouldDontSeeSmartyTagSeeIndexPage()
{
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertDontSee('<%');
    $response->assertDontSee('%>');
}
```

這樣測試就會失敗了，我們下個一目標就是讓這個測試通過即可！

另外 Smarty 有定義了全域變數如下：

```php
$tpl->assign('config', [
    'debug' => DEBUG_MODE,
    'per_page' => PER_PAGE,
    'per_top_list' => PER_TOP_LIST
]);
```

這可以直接在樣版開頭先定義：

```
@php
    $config = [
        'debug' => DEBUG_MODE,
        'per_page' => PER_PAGE,
        'per_top_list' => PER_TOP_LIST
    ];
@endphp
```

這樣就能在樣版裡取得 `$config` 變數了。

## 重構三循環

有了以上方法，再來就是進入[重構三循環](https://docs.google.com/presentation/d/1k8YKDHQb6cO_zOWdo0JW3-JP7Z5TjTSl9h_n1ItYMp4/edit#slide=id.g186f522bba_0_15)了：

1. 加測試，確認原本的行為是正常的
2. 複製程式，但不修改直接讀取，確認測試會被破壞
3. 修改程式，讓測試通過

會這樣設計的原因是，我們要確保重構的目標，是真的有被測試涵蓋到的。如果第二步測試沒被破壞的話，就得注意是不是測試不足，或是程式改錯地方等等。

這跟 TDD 三循環要做的事很像， TDD 的循環是這樣的：

1. 加測試，確認測試結果是紅燈
2. 寫程式，直到測試 pass
3. 重構程式，直到覺得程式可行，且測試也是通過

不一樣的地方在，加測試的時候， TDD 預期是會失敗，重構則預期會通過；TDD 寫程式是要讓測試通過，重構則是加程式預期會讓測試失敗。

---

今天需要程式碼調整有點多，因此只調整兩個樣版，已放在 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/13) 。其他先放著，等後面需要調的時候再來改。

[Smarty]: https://www.smarty.net/
[Blade]: https://laravel.com/docs/5.5/blade

* * *
Go to next:
[day26](./day26.md)