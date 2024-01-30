<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\PrimaryCategory;
use App\Mail\TestMail;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        //受信者の指定 
        Mail::to('test@example.com') 
            //Mailableクラス
            ->send(new TestMail()); 

        $categories = PrimaryCategory::with('secondary')
            ->get();

        $products = Product::availableItems()
            // カテゴリーを選んでない場合は初期値0を渡す
            ->selectCategory($request->category ?? '0')
            ->searchKeyword($request->keyword)
            ->sortOrder($request->sort)
            ->paginate($request->pagination ?? '20');

        return view('user.index', 
            compact(
                'products',
                'categories'
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
