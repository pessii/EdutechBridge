<?php 
namespace App\Services; 
use App\Models\Product; 
use App\Models\Cart; 

class CartService 
{ 
    public static function getItemsInCart($items) 
    { 
        //空の配列を準備
        $products = [];  
        
        // カート内の商品を一つずつ処理 
        foreach($items as $item){ 
            //オーナー情報
            //1つの商品を取得
            $p = Product::findOrFail($item->product_id); 
            //オーナー情報の取得をして配列に
            $owner = $p->shop->owner->select('name', 'email')->first()->toArray();
            //連想配列の値を取得
            $values = array_values($owner); 
            $keys = ['ownerName', 'email']; 
            $ownerInfo = array_combine($keys, $values);

             // 商品情報
            $product = Product::where('id', $item->product_id) 
                ->select('id', 'name', 'price')->get()->toArray();

            // 在庫
            $quantity = Cart::where('product_id', $item->product_id) 
                ->select('quantity')->get()->toArray();

            // 配列の結合
            $result = array_merge($product[0], $ownerInfo, $quantity[0]); 
            
            //配列に追加
            array_push($products, $result); 
        } 
        // 新しい配列を返す 
        return $products; 
    } 
}