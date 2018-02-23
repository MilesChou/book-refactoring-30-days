# SOLID 之 開關原則（Open-close principle）

原文定義是這樣子的：

> Software entities (class, modules, functions, etc.) should be open for extension, but closed for modification.

直接翻成中文大概會是：「軟體實體應該對擴展開放，對修改關閉」。這到底在說什麼呀？

說明前，先來看個小範例：

```php
<?php

class DataResource
{
    public function getData()
    {
        // 下載資料
        // ...
        $content = curl_exec($ch);
        
        // 載入 XML
        $data = simplexml_load_string($content);
        
        // 解析 XML
        // ...

        return $data;
    }
}

// Context
$dataResource = new DataResource();
$data = $dataResource->getData();
```

這是一個簡單從 API 下載 XML 並解析的小 class 。某天 API 單位說要開新 API 改使用 JSON 作回傳格式，但還在測試階段，可以先行測試，下個禮拜將會上線。

請問，這時大家會如何修改這個程式呢？

太簡單了，相信大部分的人應該會想要把解析 XML 的程式改掉，如下：

```php
<?php

class DataResource
{
    public function getData()
    {
        // 下載資料
        // ...
        $content = curl_exec($ch);
        
        // 解析 JSON
        $data = json_decode($content);
        
        // ...

        return $data;
    }
}

// Context
$dataResource = new DataResource();
$data = $dataResource->getData();
```

改好測好後，繼續開心的開發其他功能。過了一個禮拜， API 團隊突然說有重大 issue 無法如期上線，可是其他功能要依續上線，不應該因為 API 延期而延期，該怎麼辦呢？

最保險的方法當然是接 XML ，因為那是舊有還在線上維運的 API 。可是瑞凡，程式都被刪光光，回不去了。

當然版控或是備份還原都能解決，不過我們也可以從設計上解決－－**重構成新舊規格都可以用的程式！**

我們先使用 `if` 快速實作試看看：

```php
<?php

class DataResource
{
    public function getData()
    {
        // 下載資料
        // ...
        $content = curl_exec($ch);
        
        // 先假設要使用舊的 XML
        if (false) {
            // 解析 JSON
            $data = json_decode($content);
            
            // ...
        } else {
            // 載入 XML
            $data = simplexml_load_string($content);
            
            // 解析 XML
            // ...
        }

        return $data;
    }
}

// Context
$dataResource = new DataResource();
$data = $dataResource->getData();
```

如果還記得[單一職責原則][Day 7]的話，會發現它有濃濃的[壞味道][Day 4]－－ XML 處理是一種職責、 JSON 處理應該是另一種職責。

因為它們都是在解析 `$content` 因此我們可以抽出一個抽象方法 `parse` ：

```php
<?php
// 由環境來決定功能是否開啟
define('TOGGLE_ON', getenv('TOGGLE_ON'));

abstract class DataResource
{
    public function getData()
    {
        // 下載資料
        // ...
        $content = curl_exec($ch);
        
        $data = $this->parse($content);

        return $data;
    }
    
    abstract protected function parse($content); 
}

class XmlResource extends DataResource
{
    protected function parse($content)
    {
        // 載入 XML
        $data = simplexml_load_string($content);
        
        // 解析 XML
        // ...
        
        return $data;
    }
}

// JSON 實作先等一下
// Context
$dataResource = new XmlResource();
$data = $dataResource->getData();
```

截至目前為止，我們使用了樣版方法模式（Template Method Pattern），把解析資料抽離出另一個 class 實作，它同時也符合了單一職責原則。

現在，我們來回想一下需求：「新 API 改使用 JSON 作回傳格式」，可以怎麼實作呢？相信大家會換另一種方法：「寫新的`JsonResource` class 繼承 `DataResource` ，再把使用的地方改成新 class 就好，這太簡單了！」

```php
class JsonResource extends DataResource
{
    protected function parse($content)
    {
        // 解析 JSON
        $data = json_decode($content);
        
        // ...
        
        return $data;
    }
}

// Context
$dataResource = new JsonResource();
$data = $dataResource->getData();
```

同時也回想一下今天的主題：「軟體實體應該對擴展開放，對修改關閉」

* **對擴展開放**：新功能是用寫新的程式碼擴展出來的。
* **對修改關閉**：新功能不用修改現有程式碼。

相信這樣大家對開關原則應該有更深的了解了。

## 優點

最大的好處正是降低修改風險。思考一下，前面的修改，是修改既有程式碼，因此有可能破壞原有功能；後面重構後的修改，只有新增程式碼，舊有程式因為沒修改，所以理論上問題當然會比較少。

## 潛在問題

擴展的情境並不一定在設計階段就會發現，常常要到了需求調整才會知道，像上面的範例正是如此，誰會知道 API 團隊突然要改 JSON 呢？但我們還是有辦法面對改變的－－透過重構讓設計可以更符合需求。

## 參考資料

* [亂談軟體設計（2）：Open-Closed Principle](http://teddy-chen-tw.blogspot.tw/2011/12/2.html)

[Day 4]: /docs/day04.md
[Day 7]: /docs/day07.md

* * *
Go to next:
[day09](./day09.md)