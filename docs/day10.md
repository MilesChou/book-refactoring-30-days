# SOLID 之 介面隔離原則（Interface segregation principle）

> Clients should not be forced to depend on methods that they do not use.

直譯：「客戶不應該被強迫依賴他們不使用的方法」

我們來看個例子：有隻瑪爾濟斯（Maltese），愛吃也愛散步；主人訓練它握手，客人來都會表演一下，它的實作如下：

```php
<?php
interface Dog
{
    public function eat();
    public function walk();
    public function handshake();
}

class Maltese implements Dog
{
    public function eat()
    {
        print '吃飯皇帝大！';
    }
    
    public function walk()
    {
        print '出來放風囉！';
    }
    
    public function handshake()
    {
        print '我會握手哦！';
    }
}

$myMaltese = new Maltese();
$myMaltese->eat();
$myMaltese->walk();
$myMaltese->handshake();
```

思考一下，如果這時來了新的家庭成員－－雪納瑞（Schnauzer），雖然它長的很像瑪爾濟斯，但因為剛來到主人家，它還不會握手，這時 `handshake` 的實作就會很奇怪：

```php
<?php

class Schnauzer implements Dog
{
    public function eat()
    {
        print '吃飯皇帝大！';
    }
    
    public function walk()
    {
        print '出來放風囉！';
    }
    
    public function handshake()
    {
        throw new Exception('主人哩咧共蝦會');
    }
}

$mySchnauzer = new Schnauzer();
$mySchnauzer->eat();
$mySchnauzer->walk();
$mySchnauzer->handshake();   // 失控的雪納瑞
```

為什麼會這樣？因為我們在定義狗（`Dog`）的時候，應該從思考狗有哪些**行為**開始，像一般的狗都會有 `eat` 與 `walk` 的行為，而 `handshake` 是主人教完才會的行為。

而因為雪納瑞不會握手，硬要把它實作出來也怪怪的，好吧！那只好丟例外。但這樣就違反[里氏替換原則][Day 9]了，因為這很有可能在子類替換父類時，發生非預型的行為，程式也會因此變得非常不穩定。

針對這個問題，必須小小重構一下，才能順利替換。

換子類前一定要先拆介面：

```php
<?php
interface Dog
{
    public function eat();
    public function walk();
}

interface Show
{
    public function handshake();   
}
```

改成兩個介面之後，瑪爾濟斯和雪納瑞在實作 `handshake` 時，就不會起爭議了。接著來調整實作：

```php
<?php

class Maltese implements Dog, Show
{
    public function eat()
    {
        print '吃飯皇帝大！';
    }
    
    public function walk()
    {
        print '出來放風囉！';
    }
    
    public function handshake()
    {
        print '我會握手哦！';
    }
}

class Schnauzer implements Dog
{
    public function eat()
    {
        print '吃飯皇帝大！';
    }
    
    public function walk()
    {
        print '出來放風囉！';
    }
}
```

這次雪納瑞的實作就比較公道了，場景範例程式如下：

```php
<?php

class Context
{
    public function feed(Dog $dog)
    {
        $dog->eat();
    }
    
    public function play(Show $player)
    {
        $player->handshake();
    }
}

$context = new Context();

// 只要是狗都能餵食
$context->feed(new Maltese());
$context->feed(new Schnauzer());

// 只要會表演的都會握手
$context->feed(new Maltese());
// 這裡就會有「雪納瑞不會握手」的提示了
// $context->feed(new Schnauzer());
```

## 優點

遵守介面隔離原則最大的好處是，在需要多型時，會比較容易為類別實作對應方法。

## 參考資料

* [介面隔離原則](https://en.wikipedia.org/wiki/Interface_segregation_principle) | 維基百科

[Day 7]: /docs/day07.md
[Day 9]: /docs/day09.md
