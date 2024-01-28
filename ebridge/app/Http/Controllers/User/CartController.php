<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use App\Models\Stock;

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

    public function checkout()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products;

        $lineItems = []; 
        foreach($user->products as $product){ 
            // 1つずつ確認していく
            $quantity = "";
            $quantity = Stock::where('product_id', $product->id)
                ->sum('quantity');

            // カートの商品とストックテーブルの商品を確認
            if($product->pivot->quantity > $quantity){
                return redirect()->route('user.cart.index');
            } else {
                // カート内の商品(数量)の方が少なければ購入実行
                $lineItem = [ 
                    'price_data' => [
                        'product_data' => [
                            'name' => $product->name,
                            'description' => $product->description,
                        ],
                        'unit_amount' => $product->price,
                        'currency' => 'jpy',
                    ],
                    'quantity' => $product->pivot->quantity,
                ]; 
                array_push($lineItems, $lineItem); 
            }

            foreach($products as $product){
                Stock::create([
                    //作成したオーナーのIDを取得
                    'product_id' => $product->id,
                    'type' => \Constant::PRODUCT_LIST['reduce'],
                    // 1商品ずつ減らす
                    'quantity' => $product->pivot->quantity * -1
                ]);
            }
        }
        
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'success_url' => route('user.cart.success'),
            'cancel_url' => route('user.cart.cancel'),
        ]);

        $publicKey = env('STRIPE_PUBLIC_KEY');

        return view('user.checkout',
            compact(
                'session',
                'publicKey'
            )
        );
    }

    public function success()
    {
        Cart::where('user_id', Auth::id())->delete(); 

        return redirect()->route('user.items.index');
    }

    public function cancel(){ 
        $user = User::findOrFail(Auth::id());

        foreach($user->products as $product) 
        { 
            Stock::create([ 
                'product_id' => $product->id, 
                'type' => \Constant::PRODUCT_LIST['add'], 
                'quantity' => $product->pivot->quantity 
            ]); 
        }

        return redirect()->route('user.cart.index');
    } 
}
