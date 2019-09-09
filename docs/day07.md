# SOLID 之 單一職責原則（Single responsibility principle）

雖然[軟體量測][Day 6]很方便，也能找到很多可能有問題的程式碼，但最終還是需要人工檢查程式的設計。這時就需要原則（principle），讓檢視過程能有正確的方向。

SOLID 是 Robert C. Martin 提出的物件導向設計五個原則：

* **S**ingle responsibility principle (SRP)
* **O**pen-Close principle (OCP)
* **L**iskov substitution principle (LSP)
* **I**nterface segregation principle (ISP)
* **D**ependency inversion principle (DIP)

剛好字首五個字母合在一起就成為了 `SOLID` ，這五個原則目的都是為了在面對改變時，能有一套策略來應付。

今天先來講講單一職責原則：

---

首先必須先考古一下，單一職責原則（Single responsibility principle）的原文定義如下：

> A class should have only one reason to change.

大部分朋友看到「單一職責」就會聯想到，這個原則的目的是不是把 class 功能單一化？其實原文是把職責（responsibility）定義成 *one reason to change* 。

這好像有點抽象，[書中][The Principles of OOD]就有舉個例子：我們有個 Modem ，它的介面如下：

```php
// Modem
interface Modem
{
    // 撥號
    public function dial($pno);

    // 掛斷
    public function hangup();

    // 發送資料
    public function send($c);

    // 接收資料
    public function recv();
}
```

從介面上可以了解，它有一個職責是屬於連線（connection），另一個則是數據溝通（data communication）。 `dial` 、 `hangup` 是連線； `send` 、 `recv`
是數據溝通。

這樣會有什麼潛在風險呢？今天 ADSL 要升級成 100M ，我們會需要修改 Modem 實作，這會導致與它連線無關的 `send` 與 `recv` 也會跟著重新編譯與部署，風險範圍也隨之擴增。重構的方法之一，是把這個介面抽離出兩個單一職責的介面：

```php
interface Connection
{
    // 撥號
    public function dial($pno);

    // 掛斷
    public function hangup();
}

interface DataChannel
{
    // 發送資料
    public function send($c);

    // 接收資料
    public function recv();
}

class Modem implements Connection, DataChannel
{
    // ...
}
```

而原本其他依賴 Modem 介面的 class ，都依職責不同，改依賴對應的介面。

## 優點

遵守 SRP 的好處如下：

### 可讀性與可維護性提升

單一類別的複雜度降低，因為要實現的職責都很清晰明確的定義，這將大幅提升可讀性與可維護性。

### 強健性提升

如果有做好 SRP ，那修改只會對同一個介面或類別有影響，這對擴展性和維護性都有很大的幫助。

## 潛在問題

SRP 是個充滿爭議的原則。爭議的點是，那個「變化原因」會是什麼？或者說，職責該如何劃分？

因為變化原因和職責都是無法量化的，而且會因為專案需求或環境變化而改變，所以事實上 SRP 很難在專案上完美地實現。如果硬要達成 SRP 的條件，最直接的方法就是一個方法一個介面，結果會變成介面數量劇增，反而帶來更多麻煩。

## 參考資料

* [物件導向設計原則—SOLID][] | 隔壁棚的鐵人－－依恩
* [The Principles of OOD][]

[物件導向設計原則—SOLID]: https://ithelp.ithome.com.tw/articles/10191553
[The Principles of OOD]: http://www.butunclebob.com/ArticleS.UncleBob.PrinciplesOfOod
[Day 6]: /docs/day06.md

* * *
Go to next:
[day08](./day08.md)