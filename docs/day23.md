# 準備工作的簡短回顧（2）

延續昨天的話題，我們繼續來看後面幾天做了些什麼吧：

* [Day 17][] 整合 Laravel
* [Day 18][] 導入 Database Migration
* [Day 19][] 整合 CI
* [Day 20][], [Day 21][] 導入驗收測試

## 整合 Laravel

使用 Laravel 的 Route 接上舊程式：

```php
Route::get('/admin', function () {
    require_once __DIR__ . '/../admin.php';
});

Route::get('/', function () {
    require_once __DIR__ . '/../index.php';
});
```

這能讓舊程式運行在 Composer 與 Laravel 的平台之上，後面如果需要在既有程式碼裡，使用 Composer 套件或是 Laravel 時，就可以大方的使用了：

```
// 使用 Carbon
echo Carbon::now();

// 使用 Laravel Facade
DB::table('product')->select();
```

尤其如果改使用 Laravel Container 後，就能開始使用 Mock 來替換物件，讓撰寫驗收測試的難度降低。

## Database Migration

程式碼要版控，資料庫的 Schema 也要版控，資料庫版控工具都通稱為 Database Migration 。

使用 Database Migration 的好處在，資料的遷移可以由程式進行。在遷移過程，如果有需要運算或是轉換的行為，程式的處理會遠比人為處理來的可靠很多。

Schema 通常會被認為是基礎設施（infrastructure），如果 Schema 可以介由程式建立的話，這正是一種*基礎設施即程式碼*（[Infrastructure as Code][]）的概念。最明顯的好處是：只要有完整原始碼，不管是剛來的新同事，或是遠在雲端的 Travis CI ，任何地方都能做測試。

## 整合 CI

CI 會在程式碼改變的時候，建置（build）原始碼。它除了會檢查程式碼之外，也能提供[許多資訊][有了 CI Server，然後呢？]讓開發人員提早發現與解決問題。

重構有可能會改變設計，如果沒有處理好，就有可能把原始碼整個搞壞。

整合 CI 正是為了發現並解決這些問題。

## 驗收測試

對一個產品而言，做驗收測試就像是在模擬使用者操作產品。

驗收測試能確保使用者操作產品是符合預期的，這在重構時，是個非常重要的任務。修改程式該如何確定程式沒改壞？尤其是越底層的程式，如 DB 連線，就越難確認。如果有驗收測試的話，執行一下就可以確認了。

> 昨天對自動載入做調整時，也是依賴驗收測試來確保產品沒有被改壞的。

---

現在基本測試都有了，明天就能開始對程式敲敲打打了

## 參考資料

* [Infrastructure as Code][] | 維基百科
* [有了 CI Server，然後呢？][] | CI 從入門到入坑

[Infrastructure as Code]: https://en.wikipedia.org/wiki/Infrastructure_as_Code
[有了 CI Server，然後呢？]: https://github.com/MilesChou/book-intro-of-ci/blob/release/docs/day29.md
[Day 17]: /docs/day17.md
[Day 18]: /docs/day18.md
[Day 19]: /docs/day19.md
[Day 20]: /docs/day20.md
[Day 21]: /docs/day21.md
