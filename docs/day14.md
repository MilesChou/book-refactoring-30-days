# 重構的第一步－－讓程式可以動

不管程式再怎麼爛，因為之前專題有過關，至少可以它「曾經」跑得起來吧？

只要跑得起來，我們就能確認它原本的功能，同時也讓重構的結果有個驗收標準－－原本的功能要正常！

## 使用 PHP 7.2 先硬上

我們先使用 built-in server 來試試五年前的程式碼放到現在的 7.2 還能不能用：

```
$ php -S 0.0.0.0:8080
PHP 7.2.0 Development Server started at Sun Dec 24 22:05:00 2017
Listening on http://0.0.0.0:8080
Document root is /Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days
Press Ctrl-C to quit.
```

接著看打開瀏覽器，預期是一定會噴錯的，因為 MySQL 還沒準備好。

打開一看，真的噴錯了，錯誤如下：

```
Fatal error: Uncaught Error: Call to undefined function mysql_connect() in /Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days/class/mysql.class.php:53 Stack trace: #0 /Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days/class/mysql.class.php(39): db->_connect() #1 /Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days/class/shop.class.php(56): db->__construct(true) #2 /Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days/index.php(8): shop->__construct(true) #3 {main} thrown in /Users/miles.chou/GitHub/MilesChou/book-refactoring-30-days/class/mysql.class.php on line 53
```

這裡說，我們用了一個不存在的函式 [`mysql_connect()`](http://php.net/manual/en/function.mysql-connect.php) 在 `mysql.class.php` 裡，也就是昨天有提到的 SQL builder 裡。

## 換 PHP 5.4

因為目標是要讓程式能跑，所以先讓 `mysql_connect` 能用就好，官網說 5.5 就移除了，因此再來會有兩種選擇

1. 使用 5.4 啟動
2. 改程式

先使用 5.4 啟動好了， 5.4 開始也支援 built-in server 上面的啟動過程應該不大會有問題； [Docker Hub](https://hub.docker.com/) 有 5.4 的 image 可以用，來使用 Docker 起服務吧：

```
$ docker run --rm -it -v `pwd`:/source -w /source -p 8080:8080 php:5.4 php -S 0.0.0.0:8080
```

## 再換 PHP 5.3 + Apache

但 5.4 起來後，它還是說 `mysql_connect` 找不到？算了換 5.3 ，不過 5.3 就沒有 built-in server ，必須換 apache ：

```
$ docker run --rm -it -v `pwd`:/var/www/html -w /var/www/html -p 8080:80 php:5.3-apache
```

這次出現的訊息總算不一樣了：

```
Warning: mysql_connect() [function.mysql-connect]: Can't connect to local MySQL server through socket '/var/run/mysqld/mysqld.sock' (2) in /var/www/html/class/mysql.class.php on line 55
Error with MySQL connection
Warning: mysql_close() expects parameter 1 to be resource, boolean given in /var/www/html/class/mysql.class.php on line 61
```

它說無法建立連線。這是當然的，因為 MySQL 還沒起來啊！所以下一步，把 Docker MySQL 跑起來：

```bash
docker run -d -e MYSQL_ROOT_PASSWORD=password -p 3306:3306 -v `pwd`:/source --name some-mysql mysql:5.6
```

塞資料：

```bash
docker exec -it some-mysql bash
# 進到 Docker 裡
mysql -ppassword < /source/sql/localhost.sql
# 離開 Docker 
exit
```

再重開 PHP

```bash
docker run --rm -it --link some-mysql:mysql -v `pwd`:/var/www/html -w /var/www/html -p 8080:80 php:5.3-apache
```

再來要進到設定檔（`config.php`）調整 DB 設定：

```php
define('DB_HOST', 'mysql');
define('DB_USER', 'root');
define('DB_PASS', 'password');
```

這樣做完雖然會有些時區的錯誤訊息，但是應該就會出現畫面了。

今天先做到這樣，至少已經確定程式能跑在 Docker PHP 5.3 + Apache + MySQL 5.6 的環境了。但 5.3 實在很尷尬，明天就試著把程式改到 7.x 看看！

* * *
Go to next:
[day15](./day15.md)