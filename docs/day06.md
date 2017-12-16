# 軟體量測（Software Metric）

寫出好維護的程式要靠經驗累積的，初學程式經驗少，因此容易寫出有[壞味道][Day 4]的程式。而有經驗的開發者，看到壞味道一定很敏感。但是檢查原始碼的狀況，也是得看人品。運氣不好，一個 commit 上千行，味道千奇百怪，依賴錯縱複雜，想提修改建議也不知從何講起。

除此之外還會有另一個問題：如果公司有一個超級開發者，不管是怎樣的程式交給他檢查，都能提出中肯的建議，那很有可能他就會成為公司的瓶頸。因為高手只有一個，檢查的程式碼也有限，勢必會演變成排隊檢查，最後就容易變成瓶頸。

難道就完全沒有辦法了嗎？有的，正所謂「成功的經驗很難複製，失敗的經驗要複製反而很簡單」，原始碼也是。換句話說，難維護的原始碼通常都有一些模式，只要是有固定規則的任務，正是電腦擅長處理的。我們可以透過工具來搜尋有固定模式的壞味道，讓每個團隊成員，都有辦法了解程式碼目前現狀，甚至有的工具還會提出修改建議，非常方便。

這些工具通常會先解析原始碼，取得一些基本數據，如原始碼行數（[loc][]），再透過這些數據來組合成各式各樣的資訊，如 Class 行數太多等等或是 Class 之間的耦合等等（[Coupling][]）。這類檢查不需要執行程式，只要有原始碼即可處理，因此我們也稱之為靜態程式分析([static program analysis][])；相反地，有些工具必須執行程式才能取得數據，如單元測試取得程式碼覆蓋率（[Code coverage][]。這類檢查則稱之為動態程式分析（[dynamic program analysis][]）。

這類工具都會對原始碼做分析（source code analyzer），我們也稱之為軟體量測（[Software Metric][]）。網路上應該能找到蠻多類似的工具，如 [PMD][] 有實作多種語言， PHP 則可以用 [PhpMetrics][] 。

以下介紹軟體量測能得知的訊息，以及我們接下來該做什麼樣的處理。

> 這類檢查非常學術，因此筆者也只知道部分數據所代表的意義。

## 原始碼行數（Line of Code）

這應該是最好懂的指標，它會把註解去除後，計算所有程式碼的行數。也有其他衍生的指標如 LLOC （Logical lines of code） 、 CLOC （Comment lines of code） 、 Volume 等等。

程式碼行數越多，閱讀理論上會花比較多時間，但不是絕對，比方說下面這段程式碼 LOC 是 2：

```php
for (i = 0; i < 100; i++) echo 'hello'; // 印出 100 個 hello
echo 'world';
```

下面這段程式碼 LOC 是 6：

```php
// 印出 100 個 hello
for (i = 0; i < 100; i++) {
    echo 'hello';
}

echo 'world';
```

相信大家應該會覺得下面的比較好懂，它用了較多的行數來表示它的結構，會比上面的好理解。

## 循環複雜度（Cyclomatic complexity）

簡單地說，它代表著原始碼有多複雜。

如果一個 method 裡，如果沒有使用 `if` 或 `for` 等，需要條件判斷的區塊，那這個 method 的循環複雜度 會是 **1** 。但開始使用 `if` 時，循環複雜度就會開始增加，反應在閱讀程式碼上就是：看到 `if` 時，都會需要停一下，了解程式什麼時候會進入 if 區塊，什麼時候不會。循環複雜度越高，理解就會比較困難，修改難度也會提高，修改程式碼的時間自然就會更長，因此它會是重構的好目標。

另外，循環複雜度也跟 coverage 有關係。如果想達到 100% ，理論上就必須寫出循環複雜度 + 1 個 test case 。 

## 程式碼覆蓋率 （Code Coverage）

它代表著單元測試曾經走過哪些路或走過哪幾個路徑。通常會用 C1 與 C2 來表示， C1 是行數的覆蓋率， C2 則是路徑的覆蓋率。

程式碼覆蓋率能提供的資訊很有限，但搭配其他資訊會得知一些訊息。比方說複雜度高的程式， bug 發生機率會比較高，如果覆蓋率又不高的話，程式的潛在風險就會非常高。

## 今日回顧

軟體量測的目的，是了解程式碼的現況，並不是設定一個標準數字讓大家完全遵守。

比方說：複雜度太高的程式確實是有風險的，但如果商業邏輯本身複雜的話，複雜度高是正常的。又或是用盡各種方法把程式碼覆蓋率提高到 100% ，但卻沒有測邊界值，這樣的程式依然是有風險的。

但量測所得到的現況，剛好可以拿來做重構方向的參考，或是重構前後的比較，因此了解這些指標，也會對重構有幫助的。

## 參考資料

* [PMD][]
* [循環複雜度（Cyclomatic complexity）][]

[循環複雜度（Cyclomatic complexity）]: https://en.wikipedia.org/wiki/Cyclomatic_complexity
[static program analysis]: https://en.wikipedia.org/wiki/Static_program_analysis
[dynamic program analysis]: https://en.wikipedia.org/wiki/Dynamic_program_analysis
[PMD]: https://pmd.github.io/
[PhpMetrics]: http://www.phpmetrics.org/
[Software Metric]: https://en.wikipedia.org/wiki/Software_metric
[Coupling]: https://en.wikipedia.org/wiki/Coupling_(computer_programming)
[Code coverage]: https://en.wikipedia.org/wiki/Code_coverage
[loc]: https://en.wikipedia.org/wiki/Source_lines_of_code
[Day 4]: /docs/day04.md
