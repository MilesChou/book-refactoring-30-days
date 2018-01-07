# 建立 Eloquent

昨天在把 View 轉換成 Blade 時，會遇到一個重大的難題：我們沒有假資料建立方法可以方便地做自動化測試。

今天會來建立 [Eloquent][] ，後面測試如果需要假資料就會比較容易。

## 建立 entity

首先 Eloquent 的設計是一個資料表，配一個 entity class ，設計如下：

```php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';

    public $timestamps = false;
}

class Product extends Model
{
    protected $table = 'product';

    public $timestamps = false;

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category', 'id');
    }
}

class ProductCategory extends Model
{
    protected $table = 'product_category';

    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class, 'category', 'id');
    }
}
```

接著把產生假資料的方法寫到 `database/factories/ProductFactory.php` ：

```php
use Faker\Generator as Faker;

$factory->define(App\Product::class, function (Faker $faker) {
    return [
        // 略
    ];
});

$factory->define(App\ProductCategory::class, function (Faker $faker) {
    return [
        // 略
    ];
});

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        // 略
    ];
});
```

測試如果一直建假資料的話，資料庫有可能會爆炸。但在爆炸之前，更為麻煩的問題是，因為資料庫會一直保存狀態，所以每次測試的狀態都無法確定，在這狀況下測試的結果就有可能失真。最常見也最困擾的狀況是：有時候測試會過，有時候不會過。

對於這個問題， Laravel 有提供兩種資料庫還原的方法，一個是 `DatabaseMigrations` ，另一種是 `DatabaseTransactions` 。

`DatabaseMigrations` 原理比較單純，它是執行測試前，會先 `rollback` ，再重新 `migrate` 。而 `DatabaseTransactions` 則是利用執行 `beginTransaction()` 時做測試，而在測試結束後 `rollback()` 還原資料庫內容。

很明顯地， `DatabaseTransactions` 肯定比較快，但它有先天上的限制：必須要同一條 connection 才有辦法取得測試狀態（transaction 狀態）的資料。但因為 Laravel 與原本專案硬幹的 SQL Builder 是使用不同的 connection ，所以只能選擇 `DatabaseMigrations` 。

實際撰寫的測試程式如下：

```php
namespace Tests\Unit;

use App\ProductCategory;
use App\Shop\Shop;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function shouldGetAllCategoryWhenSeedFactoryCategory()
    {
        /** @var ProductCategory $excepted */
        $excepted = factory(ProductCategory::class)->create();

        $target = new Shop(true);

        $actual = $target->allCategory();

        // FIXME: 因型態不同，先使用 equals
        $this->assertEquals($excepted->id, $actual[1]['id']);
        $this->assertSame($excepted->title, $actual[1]['title']);
    }
}
```

Browser 的測試範例如下：

```php
use App\ProductCategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function shouldSeeNewCategoryWhenCreateNewCategory()
    {
        /** @var ProductCategory $category */
        $category = factory(ProductCategory::class)->create();

        $this->browse(function (Browser $browser) use ($category) {
            $browser->visit('/')
                ->assertSee('產品分類')
                ->assertSee('未分類')
                ->assertSee($category->title);
        });
    }
}
```

也因為可以產生假資料，很多底層的單元測試都能開始寫了，如 `Shop::oneCategory()` ：

```php
/**
 * @test
 */
public function shouldGetOneCategoryWhenSeedFactoryCategory()
{
    /** @var ProductCategory $excepted */
    $excepted = factory(ProductCategory::class)->create();

    $target = new Shop(true);

    $actual = $target->oneCategory($excepted->id);

    // FIXME: 因型態不同，先使用 equals
    $this->assertEquals($excepted->id, $actual['id']);
    $this->assertSame($excepted->title, $actual['title']);
}
```

今天寫的單元測試將會在明天派得上用場，請大家先看 [GitHub PR](https://github.com/MilesChou/book-refactoring-30-days/pull/14) 吧！

## 參考資料

* [Eloquent][]
* [Factories][]

[Eloquent]: https://laravel.com/docs/5.5/eloquent
[Factories]: https://laravel.com/docs/5.5/database-testing#writing-factories
