<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Shop\Shop;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function main(Request $request, Shop $shop)
    {
        $id = $request->query('id');

        return view('admin.product', [
            'one' => $shop->one($id),
            'all' => $shop->all(),
            'all_category' => $shop->allCategory(),
        ]);
    }
}
