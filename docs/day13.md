# 看到 code 寫成這樣我也是醉了

今天開始，我們要一起來面對骯髒的程式碼了。

隨便找路邊的程式碼來重構，好像也不是很好，所以筆者拿五年前寫的[程式碼](https://github.com/MilesChou/shopcart)，先讓大家看看它有多髒，我們接下來幾天會好好重構它。

重構後的程式碼將會放在 [`example`](https://github.com/MilesChou/book-refactoring-30-days/tree/example) 分支，會依不同天做的事情做 commit 和 PR ，方便未來可以查看記錄。

## 介紹

這份程式碼是寫一個線上下單系統用來交差專題的，當初使用 [WAMP](http://www.wampserver.com/en/) 與 [Nodepad++](https://notepad-plus-plus.org/) 開發。

因為是剛學 PHP ，寫出來的東西非常沒有品質，也完全忘了它在做什麼了。還好文件有保留，包括安裝方法與 SQL 都還在，至少要初始化專案不會太困難。

## 接下來要面對的挑戰

首先第一個要面臨最麻煩的問題是： PHP 版本，當初開發使用 5.2 或 5.3 ，現在要直升到 7.x 是否會發現不可預期的問題？

再來也是麻煩的問題： [Smarty](https://www.smarty.net/) 原始碼需要移除，不管最後結果是否要用，其他專案的原始碼不應該被加入版控系統中。因此需要導入 Composer ，讓專案的依賴是建置時期才從外界載入。

接著是框架和資料庫套件，我們必須要用比較好的框架或套件，來取代原本筆者硬刻的[路由](https://github.com/MilesChou/book-refactoring-30-days/blob/example/index.php)和 [SQL builder](https://github.com/MilesChou/book-refactoring-30-days/blob/example/class/mysql.class.php) 。

重構最終的目的，主要還是讓其他人能快速了解程式碼，因此寫測試和文件是不可或缺的。

---

原始碼本身不難，但因為它難以修改，可能不會有人願意維護這個專案吧！

明天開始會一步一步地把它調整成接近現代 PHP 的架構。

* * *
Go to next:
[day14](./day14.md)