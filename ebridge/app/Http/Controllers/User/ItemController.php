<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');
    }

    public function index()
    {
        $products = Product::availableItems()->get();

        return view('user.index', 
            compact(
                'products'
            )
        );
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        $quantity = Stock::where('product_id', $product->id)->sum('quantity');

        // 数量が10より多かったら10にする
        if($quantity > 10){ 
            $quantity = 10; 
        }

        return view('user.show',
            compact(
                'product',
                'quantity'
            )
        );
    }
}
