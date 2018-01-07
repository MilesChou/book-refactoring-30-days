<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string datetime
 * @property string name
 * @property string email
 * @property string phone
 * @property string address
 * @property string data
 * @property int total
 * @property string sn
 * @property boolean _checkout
 */
class Order extends Model
{
    protected $table = 'order';

    public $timestamps = false;
}
