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

        $this->middleware(function($request, $next){
            //productのid取得 
            $id = $request->route()->parameter('item');
            if(!is_null($id)){
                $itemId = Product::availableItems()->where('products.id', $id)->exists();
                // 同じでなかったら 
                if(!$itemId){ 
                    abort(404);
                }
            } 
            return $next($request); 
        });
    }

    public function index(Request $request)
    {
        $products = Product::availableItems()
            ->sortOrder($request->sort)
            ->paginate($request->pagination ?? '20');

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
