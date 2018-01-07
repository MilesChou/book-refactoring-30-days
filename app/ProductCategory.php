<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string title
 * @property Product products
 */
class ProductCategory extends Model
{
    protected $table = 'product_category';

    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class, 'category', 'id');
    }
}
