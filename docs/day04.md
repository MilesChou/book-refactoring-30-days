# 開發者能察覺的壞味道（Bad Smell）

[昨天][Day03]提到，開發者都是在第一線直接被技術債凌虐，是最有感覺的苦主。

在談[技術債][Day02]的時候曾說過：「事後被別人發現的才叫 bug ，自己開發當下發現的不算」，技術債也是，設計不良的做法，當下覺得不對勁就可以立即重構改善設計；如果不重構，在未來被其他人抱怨時，就會成為技術債。

這不對勁的感覺，在重構這本書裡稱之為「壞味道」。壞味道也是一種隱喻，大概就像是：第一眼看到程式碼時，先皺個眉頭，接著歪著頭，一手托著下巴，另一手操作程式碼畫面上下移動，最後語重心長地「嗯」了一聲。相信旁邊的同事放屁也會有一樣很不舒服的感覺。

> 壞味道也很像另一種類似的概念：*反模式（Anti-patterns）*

以下會使用 PHP 語言，列幾個經典的壞味道，同時也會解釋為何會很不舒服，與比較好的解決方法。

## 不一致的排版與命名規則

第一次跟爛 code 相見歡的時候，看到大括號有時在上有時在下：

```php
function bar()
{
    echo 'Hello World';
}

function foo() {
    echo 'Hello World';
}
```

空白 tab 傻傻分不清楚：

```php
$hello = 'Hello';

if ($hello === '') {
    echo 'Hello World';
} else {
        echo $hello . ' World';
}
```

運算子與運算元有時有間隔，有時黏在一起：

```php
$world = 'World';

echo 'Hello ' . $str;

echo 'Hello '.$str;
```

函式或變數的命名有時小駝峰，有時候又底線分隔，還有全小寫擠在一起的是來亂的嗎？

```php
$fooBar = 'Hello ';

$bar_foo = 'World';

$helloworldfoobar = $fooBar . $bar_foo;

echo $helloworldfoobar;
```

在閱讀程式碼的過程中，這些不一致的程式碼，會不定時讓開發者的思緒中斷，非常不舒服。

這是因為風格不一致所造成的。這就像三十天鐵人賽交給三十個寫作高手寫，會因為每個人寫作風格不同造成閱讀上的困難，反而會不如一個人寫三十天來的好。

### 解法

排版是有工具可以自動化解決的，如常見的 IDE 或 Composer 上都能找得到對應的工具，但命名規則工具就只能做到檢查，修改只能靠手動。

## 四處可見重複的程式碼或結構

最近在翻舊程式看到這段程式碼：

```php
$params = [
    'a' => 1,
    'b' => 2,
    'c' => 3,
];

$query = '';

foreach ($params as $key => $value) {
    if ('' === $query) {
        $query .= $key . '=' . $value;
    } else {
        $query .= '&' . $key . '=' . $value;
    }
}
```

仔細看程式碼並思考一下，其實不難理解，這是在組超連結的 query 。於是，在需要組超連結的時候，就會看到這段 `foreach` 的程式碼。

事實上，程式碼不難懂，但每次閱讀程式碼的時候，都需要停下來花點時間思考這段程式碼的用途；而且程式碼的內容並不算少，如果裡面有一點點差異，是很難發現的。如下面這段程式碼乍看之下跟上面的程式碼是一樣的，但事實上是有 bug 的。

```php
$query = '';

foreach ($params as $key => $value) {
    if ('' === $query) {
        $query .= '&' . $key . '=' . $value;
    } else {
        $query .= $key . '=' . $value;
    }
}
```

如果有踩過這種雷，那停下來思考的時間就會更久，思緒中斷的會非常強烈，感覺也更不舒服。

### 解法

上述問題的標準解是使用 PHP 內建函式 [`http_build_query()`](http://php.net/manual/en/function.http-build-query.php)：

```php
$query = http_build_query($params);
```

是不是簡單許多了？

如果是有點像又不太像的重複程式碼，未來有機會的話，將會示範如何重構。

## 過肥的 method 或 class

Method 太肥的狀況是這樣的：

```php
function foo($params) {
    $result = [];
    $result[] = $params['bar'];

    //
    // 想像中間有 100 行程式碼
    //
    
    $result[] = $bar;
    
    return $result; 
}
```

這樣的 method 最大的問題在 `$result` 最一開頭就定義，中間 100 行程式碼中，可能都有操作這個變數。所以當遇到需要了解這個 method ，比方說 return 結果不對時，很有可能需要把這 100 行可能操作回傳值的程式碼全部看完，才會找到問題所在。

另一個問題是， method 越大做的事越多，代表依賴也越多；依賴多就有可能會發生：依賴 A 了解完後，要了解依賴 B 時，會有 context 切換，因此會有思緒中斷，這種的感覺也是很不舒服。

Class 過肥的情況也是類似的。

### 解法

Scrum 通常會先有 story ，然後開始切 task 再開始工作。 Story 可能很大，跟一個 100 行的 method 一樣大，所以我們 method 應該是有辦法切成小 method 來組合出主 method 要的功能。

## 今日回顧

* 這些壞味道的共同點都是，會讓開發者感到思緒中斷
* 如果我們可以察覺壞味道的話，就有辦法儘可能不債留子孫

[Day02]: /docs/day02.md
[Day03]: /docs/day03.md
