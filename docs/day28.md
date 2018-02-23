# 重構 Controller

雖然 Controller 昨天切成兩個了，但是裡面還是亂七八糟，今天的目標是要把裡面盡可能的整理。

## 使用 Service Provider

`Shop` 與 `Mysql` 在這個應用程式裡，是屬於單例的角色。我們可以把這兩個物件，放在 Laravel Container 裡，透過 DI 注入讓 Controller 取得，這樣程式會更加簡潔。

首先我們先處理 `Mysql` ：

```php
// app/Providers/AppServiceProvider.php

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Mysql::class, function ($app) {
            // 定義 Mysql 物件如何建構

            require base_path('config.php');

            return new Mysql(env('APP_DEBUG', false));
        });
    }
}
```

如此在使用 `app(Mysql::class)` 就能取得到 `Mysql` 單例物件了。接著在調整 `Shop` 的建構子：

```php
public function __construct(Mysql $mysql)
{
    $this->_db = $mysql;
}
```

這樣在使用 `app(Shop::class)` 時， Laravel 會發現要注入 `Mysql` 物件，所以會接著用 `app(Mysql::class)` 拿到 `Mysql` 物件後，再繼續把 Shop 物件建構出來。

Controller 的 Action 也有一樣的功能：當定義 `Shop` 物件時， Laravel 會使用 `app(Shop::class)` 取得 `Shop` 物件再傳入 action ：

```php
public function index(Request $request, Shop $shop)
{
}
```

如此一來，就不需要在 controller 裡面建構 `Shop` 物件，而能專注在如何操作 `Shop` 物件。

同理 Smarty 也能做一樣的處理：

```php
$this->app->singleton(Smarty::class, function () {
    $instance = new Smarty;
    $instance->template_dir = base_path('/templates/');
    $instance->compile_dir = base_path('/templates/compile/');
    $instance->config_dir = base_path('/templates/configs/');
    $instance->cache_dir = base_path('/templates/cache/');
    $instance->caching = false;
    $instance->auto_literal = false;
    $instance->left_delimiter = '<%';
    $instance->right_delimiter = '%>';

    $instance->assign('config', [
        'debug' => DEBUG_MODE,
        'per_page' => PER_PAGE,
        'per_top_list' => PER_TOP_LIST
    ]);

    return $instance;
});
```

> 完成後， coverage 累積上升 1.98% 。

## 移除 config.php

上面做完之後，會發現 `config.php` 的內容都是定義常數，因此可以移到 Service Provider 裡的 `boot()` 方法，接著就能移除它了。

帳號密碼目前的設計也是常數，所以先暫時也用常數處理。

> 完成後， coverage 累積上升 2.76% 。

## 拆出獨立的 action

如 contact 的功能相對獨立，可以另外拆出一個 Controller 來處理：

```php
class ContactController extends Controller
{
    public function index()
    {
        return view('shop.contact');
    }
}
```

調整 Route ：

```php
Route::get('/contact', 'ContactController@index');
```

調整 View ：

```php
<a href="/contact">聯絡我們</a>
```

測試也得調整：

```php
$browser->visit('/contact')
```

這樣就能把一個頁面移出另一個 controller 了。

> 同樣的方法也可以用在 admin 登入頁。

## 使用 Request

Laravel 的套件 `Request` 提供了許多取得 HTTP request 的方法，如：

```php
if (!isset($_GET['act'])) {
    $_GET['act'] = 'main';
}

switch ($_GET['act']) {}
```

可以用下面的程式碼取代：

```php
$act = $request->query('act', 'main');

switch ($act) {}
```

> 記得要先確認其他程式碼沒有使用到 `$_GET['act']` 才能直接這樣改。

許多常用的判斷，框架都有提供更精簡的方法提供開發者使用。因此改使用這些方法，也是個提高易讀性的好辦法。

---

今天的程式可以參考 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/17) 。

上面的技巧不斷使用後，會發現 coverage 越來越高，程式也會越來越精簡好懂，這正是重構的目的與價值。

## 參考資料

* [Service Container](https://laravel.com/docs/5.5/container)
* [Service Provider](https://laravel.com/docs/5.5/providers)

* * *
Go to next:
[day29](./day29.md)