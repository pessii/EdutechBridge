<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id()); 
        $products = $user->products;
        $totalPrice = 0; 

        foreach($products as $product){ 
            $totalPrice += $product->price * $product->pivot->quantity; 
        }

        return view('user.cart',
            compact(
                'products',
                'totalPrice',
            )
        );
    }

    public function add(Request $request) 
    { 
        //カートに商品があるか確認
        $itemInCart = Cart::where('user_id', Auth::id())->where('product_id', $request->product_id)->first();  
        if($itemInCart){ 
            //あれば数量を追加 
            $itemInCart->quantity += $request->quantity;
            $itemInCart->save(); 
        } else { 
            // なければ新規作成 
            Cart::create([ 
                'user_id' => Auth::id(), 
                'product_id' => $request->product_id, 
                'quantity' => $request->quantity 
            ]); 
        } 

        return redirect()->route('user.cart.index'); 
    }

    public function delete($id)
    {
        Cart::where('product_id', $id) 
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('user.cart.index'); 
    }

}
