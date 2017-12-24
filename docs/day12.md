# 不在 SOLID 裡的 最小知識原則（Least Knowledge Principle）

會說不在 SOLID 裡，是因為在[維基][SOLID]找不到，但[書上][設計模式之禪]有寫，所以就拿出來騙天數好了。

原文：

> Each unit should have only limited knowledge about other units: only units "closely" related to the current unit.

直譯：

每個單位應該對其他單只能有有限的知識：只了解跟目前單位比較親近的單元。

程式寫了就是要拿來執行，從未執行過的程式碼比 bug 還要不如，因為連它是不是 bug 都不曉得。換言之，單元與單元之間是會有耦合的。這個原則在告訴我們該如何控制耦合的程度。

舉個例子：

有一位主管（Manager）很會寫程式，他的實作如下：

```php
<?php

class Manager
{
    public function work(Code $code)
    {
        $code->content .= ' + Manager code';
        $this->build($code->content);
    }
    
    public function build($content)
    {
        echo "$content 程式建置完畢\n";
    }
    
}

class Code
{
    public $content;
    public function __construct($content)
    {
        $this->content = $content;
    }
}

$code = new Code('Legacy code');

$manager = new Manager();
$manager->work($code);
```

執行結果如下：

```
Legacy code + Manager code 程式建置完畢
```

## 問題

程式碼看似很正常，但問題總是發生在變化的時候：假如來了新的工程師，工程師該怎麼做事？看樣子好像也只能跟著主管傳承下來的做法來做：

```php
<?php

class Engineer
{
    public function work(Code $code)
    {
        $code->content .= ' + Engineer code';
        $this->build($code->content);
    }
    
    public function build($content)
    {
        echo "$content 程式建置完畢\n";
    }
}

$code = new Code('Legacy code');

$manager = new Manager();
$manager->work($code);

$engineer = new Engineer();
$engineer->work($code);
```

執行結果如下：

```
Legacy code + Manager code 程式建置完畢
Legacy code + Manager code + Engineer code 程式建置完畢
```

雖然看似能解決問題，但這樣其實已經違反[單一職責原則][Day 7]了，因為 `Code` 只要內容物有變化，比方說 `content` 改成 `source` ，這樣會同時影響到 `Manager` 與 `Engineer` 的實作。

為什麼會這樣呢？因為主管和工程師對於程式碼的可控權限都太高了，造成程式碼的行為變化會同時影響大家操作方法。相信大家也有這樣的經驗，改流程、改框架、改佈署、改測試、改寫法等等，都會對其他人造成不小的困擾。

重構的方法也很簡單，主管要適當[授權][DevOps]團隊制定一下基本操作規範（interface）就好了，同時也把公開的東西盡可能減少。

```php
<?php

interface Source
{
    public function write($content);
    public function build();
}

class Code implements Source
{
    private $content;
    public function __construct($content)
    {
        $this->content = $content;
    }

    public function write($content)
    {
        $this->content .= " + $content";
    }

    public function build()
    {
        echo "$this->content 程式建置完畢\n";
    }
}

class Manager
{
    public function work(Code $code)
    {
        $code->write('Manager code');
        $code->build();
    }
}

class Engineer
{
    public function work(Code $code)
    {
        $code->write('Manager code');
        $code->build();
    }
}

$code = new Code('Legacy code');

$manager = new Manager();
$manager->work($code);

$engineer = new Engineer();
$engineer->work($code);
```

執行結果不變：

```
Legacy code + Manager code 程式建置完畢
Legacy code + Manager code + Engineer code 程式建置完畢
```

但重構後，我們會發現對 `content` 的細部處理改在 `Code` 裡面解決，其他人則使用 `Code` 提供的方法來達成任務。這樣的結果是讓 `Code` 的內聚性提高，程式碼就會越穩定。

今天不管是誰，只要會寫 code ，會使用 `Code` 提供的 `build()` 方法，就能參與開發。

> 這就有點像把 [Makefile][] 使用在團隊規範一樣。

```php
<?php

class Newbie
{
    public function work(Code $code)
    {
        $code->write('Newbie code');
        $code->build();
    }
}
```

## 優點

程式不可能沒有耦合，但耦合過高又會讓破壞程式碼的內聚性，最小知識原則告訴我們，要把耦合的程度適度的縮小才是最好的。

## 參考資料

* [SOLID][] | 維基百科
* [設計模式之禪][] | 秦小波
* [什麼是 DevOps ？][] | CI 從入門到入坑
* [Makefile][] | 104 Guideline

[SOLID]: https://en.wikipedia.org/wiki/SOLID_(object-oriented_design)
[設計模式之禪]: http://www.books.com.tw/products/CN11096287
[Makefile]: https://github.com/104corp/guideline/blob/master/build/makefile.md
[DevOps]: https://github.com/MilesChou/book-intro-of-ci/blob/release/docs/day01.md
[Day 7]: /docs/day07.md
