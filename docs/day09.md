# SOLID 之 里氏替換原則（Liskov substitution principle）

一樣要考古一下原文：

> Subtypes must be substitutable for their base types.

子類別必須要能取代它的父類別。

這次的考古講得非常簡單，它背後所代表的意義是：父類別出現的地方，子類別就能代替它，而且要能做到替換而**不出現任何錯誤或異常**。

文字描述依然抽象，我們繼續看昨天的例子：

```php
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
```

這裡面，父類別是 `DataResource` ，子類別是 `XmlResource` 。現在有個 Model 物件需要把資料拿出來儲存，我們可以這樣寫：

```php
class Model
{
    public $resource;
    public $storage;
    
    public function __construct(XmlResource $resource)
    {
        $this->resource = $resource;
    }
    
    public function save()
    {
        $data = $this->resource->getData();
        $this->storage->store($data);
    }
}

$model = new Model(new XmlResource());
```

但問題來了，昨天我們還有寫另一個 class 它也能取得資料呀！

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
```

可是 Model 傳入 `JsonResource` 是不能跑的！因為 Model 只認 `XmlResource` ，不認 `JsonResource` 。

```php
$model = new Model(new JsonResource());
```

解決方法其實很簡單，我們只要把 Model 的定義改成兩個子類別所繼承的 `DataResource` 父類別即可。

```php
class Model
{
    public $resource;
    public $storage;
    
    public function __construct(DataResource $resource)
    {
        $this->resource = $resource;
    }
    
    public function save()
    {
        $data = $this->resource->getData();
        $this->storage->store($data);
    }
}

$model = new Model(new XmlResource());
```

這程式能跑的原因正是一開始所提到的：「父類別出現的地方，子類別就能代替它」，但有做到「要能做到替換而不出現任何錯誤或異常」嗎。

因為 `save()` 使用的 `getData()` 是 `DataResource` 所定義的公開方法，因為繼承會把父類別的所有行為繼承到子類別，因此子類別也會有 `getData()` 而不會讓 `save()` 出錯，因此也有做到「不出現任何錯誤或異常」。

原本程式的做法，是 Model 只能依賴 `XmlResource` ，這並不符合「里氏替換原則」；改依賴 `DataResource` 後，程式就符合原則了，接著就會發現程式的擴展性變好， Model 的 Resource 就可以有多樣化選擇，除了 `XmlResource` 與 `JsonResource` 之外，甚至還可以新加 `CsvResource` 讓 Model 也能讀取 CSV 檔。

## 遵守原則的要領

為避免發生錯誤或異常，實作可以參考要領，如下：

* 子類別必須完全實作父類別的方法
* 子類別可以有屬於自己的屬性和方法
* 覆寫或實作父類別的方法時，輸入參數要與父類別定義的一樣，或是更寬鬆。比方說：父類別是 `DataResource` ，子類別則可以是 `XmlResource` 或 `DataResource`
* 覆寫或實作父類別的方法時，輸出結果可以縮小。比方說：父類別是 `JsonResource` ，子類別則可以是 `JsonResource` 或 `DataResource`

## 優點

里氏替換原則的重點是要增加程式的強健性，讓版本升級的時候也能有很好的兼容性。比方說：子類別增加或修改，並不影響其他子類別，這正是強健性的特質。

上例的使用情況是：子類別處理不同的業務邏輯，參數定義使用父類別，實際上傳遞的是子類別，這樣就能同份定義，執行不同的業務邏輯。

## 參考資料

* [亂談軟體設計（4）：Liskov Substitution Principle](http://teddy-chen-tw.blogspot.tw/2012/01/4.html)

* * *
Go to next:
[day10](./day10.md)