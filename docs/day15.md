# 來試著升級 PHP 吧

我們接下來會使用 PHPConf 2016 的簡報「[使用 Slim 為 Legacy Code 重構][]」提到的 proxy pattern 方法來重構。而中間的 Route 會使用 [Laravel][] 。只是在使用 Laravel 之前，我們得先要升級 PHP 。

[昨天][Day 14]是發現它沒有內建函式 `mysql_*` ，後面的版本都建議換用 `mysqli_*` 了。

既然如此，我們就寫一堆 `mysql_*` 函式，轉接到 `mysqli_*` 函式就好了呀！

> 這也是 [Adapter Pattern][] 的應用方法之一，只是這個狀況下，我們更改原始碼的範圍不會很大。

## 實驗看看

觀查一下程式碼，原本的主要路由 `admin.php` 與 `index.php` 背後都會引用 `config.php` ，我們寫一個 `workaround.php` 在 `config.php` 裡載入即可。

先來試試連線函式：

```php
if (!function_exists('mysql_connect')) {
    function mysql_connect($host, $user, $pass)
    {
        return mysqli_connect($host, $user, $pass);
    }
}
```

`config.php` 載入：

```php
require_once __DIR__ . '/workaround.php';
```

接著啟動服務：

```bash
docker run --rm -it --link mysql -v `pwd`:/source -w /source -p 8080:8080 php:7.2-alpine php -S 0.0.0.0:8080
```

發現居然沒有 `mysqli` ，上網可以查得到它有支援，所以看樣子是 alpine 預設沒安裝，來換個策略：先 `sh` 進去安裝後，再開伺服器：

```bash
docker run --rm -it --link some-mysql:mysql -v `pwd`:/source -w /source -p 8080:8080 php:7.2-alpine sh

# 在 Docker 裡
docker-php-ext-install mysqli
php -S 0.0.0.0:8080
```

哦哦哦！這次又出現不一樣的訊息了：

```
Warning: Use of undefined constant MYSQL_ASSOC - assumed 'MYSQL_ASSOC' (this will throw an Error in a future version of PHP) in /source/class/mysql.class.php on line 67

Fatal error: Uncaught Error: Call to undefined function mysql_query() in /source/class/mysql.class.php:68 Stack trace: #0 /source/class/mysql.class.php(41): db->init() #1 /source/class/shop.class.php(56): db->__construct(true) #2 /source/index.php(8): shop->__construct(true) #3 {main} thrown in /source/class/mysql.class.php on line 68
```

所以看起來連線是可行的，我們只要把所有函式都接上去就行了。

## 實作轉接函式

經過一連串的 try & error 後，最後 `workaround.php` 的長相如下：

```php
class Workaround
{
    public static $mysqli;
}

define('MYSQL_ASSOC', MYSQLI_ASSOC);

if (!function_exists('mysql_connect')) {
    function mysql_connect($host, $user, $pass)
    {
        return Workaround::$mysqli = mysqli_connect($host, $user, $pass);
    }
}

if (!function_exists('mysql_close')) {
    function mysql_close($link)
    {
        return mysqli_close($link);
    }
}

if (!function_exists('mysql_query')) {
    function mysql_query($query, $link = null)
    {
        if (null === $link) {
            return mysqli_query(Workaround::$mysqli, $query);
        } else {
            return mysqli_query($link, $query);
        }
    }
}

if (!function_exists('mysql_select_db')) {
    function mysql_select_db($dbname, $link)
    {
        return mysqli_select_db($link, $dbname);
    }
}

if (!function_exists('mysql_fetch_array')) {
    function mysql_fetch_array($result, $type)
    {
        return mysqli_fetch_array($result, $type);
    }
}

if (!function_exists('mysql_num_rows')) {
    function mysql_num_rows($result)
    {
        return mysqli_num_rows($result);
    }
}

if (!function_exists('mysql_real_escape_string')) {
    function mysql_real_escape_string($string, $link)
    {
        if (null === $link) {
            return mysqli_real_escape_string(Workaround::$mysqli, $string);
        } else {
            return mysqli_real_escape_string($link, $string);
        }
    }
}

if (!function_exists('mysql_errno')) {
    function mysql_errno($link = null)
    {
        if (null === $link) {
            return mysqli_errno(Workaround::$mysqli);
        } else {
            return mysqli_errno($link);
        }
    }
}

if (!function_exists('mysql_error')) {
    function mysql_error($link = null)
    {
        if (null === $link) {
            return mysqli_error(Workaround::$mysqli);
        } else {
            return mysqli_error($link);
        }
    }
}
```

會有 `Workaround` class 的目的是為了暫存 `mysqli` 連線變數。且也在 function 先做好手腳了，這樣在之後調整程式碼會比較簡單一點。

原始碼只有修改兩個地方，一個是 `config.php` 的引用，另一個是 `mysql.class.php` 有個地方把字串當陣列在用， PHP 7.2 不支援，因此只能修改了。

基本上，上面這樣就算完成了，未來就可以使用 PHP 7.2 開發了。

## 升級的過程還會有哪些雷？

筆者目前有遇過的： 5.3 to 5.6 下面這個狀況會報錯：

```php
foo(&$bar);

function foo($bar) {
}
```

要改成下面這樣

```php
foo($bar);

function foo(&$bar) {
}
```

其他就沒有遇過了， PHP 算是相容性做很好的語言。但跟大多數語言和框架一樣，升級還是無法確定一切正常，只能靠亂點測試的運氣。更好的方法是寫自動化測試，在升級的時候跑一輪即可。

只是一個長久未重構的既有程式碼（legacy code），無法簡單地寫自動化測試，只能先使用硬上的方法讓程式變得比較好測之後，再開始把測試一個一個補上去。

## 參考資料

程式碼可參考 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/2/files)

* [使用 Slim 為 Legacy Code 重構][] | PHPConf 2016
* [Adapter Pattern][] | 維基百科

[Adapter Pattern]: https://en.wikipedia.org/wiki/Adapter_pattern
[Laravel]: https://laravel.com/
[使用 Slim 為 Legacy Code 重構]: https://docs.google.com/presentation/d/1k8YKDHQb6cO_zOWdo0JW3-JP7Z5TjTSl9h_n1ItYMp4/edit
[Day 14]: /docs/day14.md

* * *
Go to next:
[day16](./day16.md)