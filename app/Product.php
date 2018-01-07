<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int category
 * @property string title
 * @property string content
 * @property string pic
 * @property int cost
 * @property int price
 * @property int store
 * @property int sale
 * @property int click
 * @property ProductCategory productCategory
 */
class Product extends Model
{
    protected $table = 'product';

    public $timestamps = false;

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category', 'id');
    }
}
