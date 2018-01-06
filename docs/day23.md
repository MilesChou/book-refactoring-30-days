# 調整程式碼風格（Coding Style）

雖然程式碼風格跟設計沒有關係，但是程式碼風格不一致，對理解程式是一個很大的阻礙。調整程式碼風格，是不會影響功能的，因此很適合拿來當重構的暖身。

PHP 的世界裡，有 *[PHP-FIG][]* （PHP Framework Interoperability Group）提出了一些建議的標準 PSR （PHP Standards Recommendations），其中有一項 PSR-2 正是在規定程式碼風格的，也有蠻多套件是遵守這個規範開發，如 *[The League of Extraordinary Packages][]* 出的套件。

PSR-2 大部分都是定義排版，有很多套件支援自動排版，如 *[PHP_CodeSniffer][]* 。以下將使用 PHP_CodeSniffer 來當作範例。

## 安裝與設定

安裝指令：

```
$ composer require --dev squizlabs/php_codesniffer
```

接著就會多一個指令是 `phpcs`

```
$ php vendor/bin/phpcs
ERROR: You must supply at least one file or directory to process.

Run "phpcs --help" for usage information
```

剛安裝好執行，會出現錯誤是正常的，因為還沒有跟 `phpcs` 說有哪些程式要做檢查。除了直接下指令給參數之外，它預設會讀取 `phpcs.xml` 設定檔做為檢查的標準，下面是一個簡單的範例：

```xml
<?xml version="1.0"?>
<ruleset>
    <!-- display progress and sniff -->
    <arg value="n"/>
    <arg value="p"/>
    <arg value="s"/>

    <!-- use colors in output -->
    <arg name="colors"/>

    <!-- inherit rules from: -->
    <rule ref="PSR2"/>

    <!-- Paths and file to check -->
    <file>app</file>
    <file>class</file>
    <file>config</file>
    <file>admin.php</file>
    <file>config.php</file>
    <file>index.php</file>
    <file>workaround.php</file>

    <!-- Don't check Smarty because it's third-party lib -->
    <exclude-pattern>class/Smarty/*</exclude-pattern>
</ruleset>
```

這次再下一次指令就會看到一堆錯誤：

```
$ php vendor/bin/phpcs
... 略
```

PHP_CodeSniffer 有提供自動修正的工具，像下面這個錯誤，有 `[x]` 符號的，就是可以自動修正：

```
 115 | ERROR | [x] Expected 1 newline at end of file; 0 found
     |       |     (PSR2.Files.EndFileNewline.NoneFound)
```

自動修正的指令是 `phpcbf`

```
$ php vendor/bin/phpcbf
```

修正完我們可以跑看看測試是否正常：

```
$ php vendor/bin/phpunit
$ php artisan dusk
```

如果沒意外的話，結果會是一切正常的！有興趣可以翻看看程式碼被改成什麼樣，舉個例子：

```php
// 修改前
if (!isset($_GET['act'])) { $_GET['act'] = 'main';}

// 修改後
if (!isset($_GET['act'])) {
    $_GET['act'] = 'main';
}
```

> 事實上，筆者以前覺得寫成一行感覺好像很厲害。現在覺得，程式碼還是寫成像下面大家都看得懂的樣子才是最好的。

還是會有很多規範是無法自動修正的，如變數與方法的命名規範，這需要靠人工調整。

人工調整跟自動調整一樣：調整完，跑 `phpcs` ，跑測試，不斷的循環，直到 `phpcs` 沒有發生錯誤。

> 人工調整其實也可以做任何事，像筆者有調成短 array 表示法 `[]`

記得 `phpcs` 也要加到 `.travis.yml` ：

```yaml
script:
- php vendor/bin/phpcs
```

但有些規則是難以實現的，像 workaround 要求要 namespace 實在有點麻煩，可以在設定檔裡面加除外：

```xml
<!-- specific and just exclude rules in some file -->
<rule ref="PSR1.Classes.ClassDeclaration.MissingNamespace">
    <exclude-pattern>workaround.php</exclude-pattern>
    <exclude-pattern>class/mysql.class.php</exclude-pattern>
    <exclude-pattern>class/shop.class.php</exclude-pattern>
</rule>
```

現在調整完後， Travis CI 應該會是正確的。接下來我們就能調整更多東西，如有些 return 是多餘的，或是單引號和雙引號的互轉等等。只要有任何錯誤， Travis CI 都會忠實的回報讓我們知道，太方便了。

程式碼可以參考 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/11)

## 參考資料

[PHP-FIG]: http://www.php-fig.org/
[The League of Extraordinary Packages]: http://thephpleague.com/
[PHP_CodeSniffer]: https://github.com/squizlabs/PHP_CodeSniffer
