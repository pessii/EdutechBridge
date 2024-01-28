<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Image;
use App\Models\Product;
use App\Models\Shop;
use App\Models\PrimaryCategory;
use App\Models\Owner;
use App\Models\Stock;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');
        //オーナーかの確認
        $this->middleware(function($request, $next){
            //productのid取得 
            $id = $request->route()->parameter('product');
            if(!is_null($id)){
                $productsOwnerId = Product::findOrFail($id)->shop->owner->id;
                // キャスト 文字列→数値に型変換 
                $productId = (int)$productsOwnerId;
                // 同じでなかったら 
                if($productId !== Auth::id()){ 
                    abort(404);
                }
            } 
            return $next($request); 
            });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ログインしているプロダクトを取得
        $ownerInfo = Owner::with('shop.product.imageFirst')
            ->where('id', Auth::id())
            ->get();

        return view('owner.products.index',
            compact(
                'ownerInfo'
            ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shops = Shop::where('owner_id', Auth::id())
            ->select('id', 'name')
            ->get();

        $images = Image::where('owner_id', Auth::id())
            ->select('id', 'title', 'filename')
            ->orderBy('updated_at', 'desc')
            ->get();

        $categories = PrimaryCategory::with('secondary')
            ->get();

        return view('owner.products.create', 
            compact(
                'shops', 
                'images', 
                'categories'
            ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        try
        {
            DB::transaction(function () use($request) {
                $product = Product::create([
                    'name' => $request->name,
                    'information' => $request->information,
                    'price' => $request->price,
                    'sort_order' => $request->sort_order,
                    'shop_id' => $request->shop_id,
                    'secondary_category_id' => $request->category,
                    'image1' => $request->image1,
                    'image2' => $request->image2,
                    'image3' => $request->image3,
                    'image4' => $request->image4,
                    'image5' => $request->image5,
                    'image6' => $request->image6,
                    'is_selling' => $request->is_selling,
                ]);

                Stock::create([
                    'product_id' => $product->id,//作成したオーナーのIDを取得
                    'type' => 1,
                    'quantity' => $request->quantity
                ]);
            //トランザクション２回繰り返し
            }, 2);
    
            return redirect()
                ->route('owner.products.index')
                ->with('message', '商品登録が完了されました');
        }
        catch(\Throwable $e)
        {
            Log::error($e);
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // １つの商品を取得
        $product = Product::findOrFail($id);
        // 在庫情報を取得
        $quantity = Stock::where('product_id', $product->id)
            ->sum('quantity');

        $shops = Shop::where('owner_id', Auth::id())
            ->select('id', 'name')
            ->get();

        $images = Image::where('owner_id', Auth::id())
            ->select('id', 'title', 'filename')
            ->orderBy('updated_at', 'desc')
            ->get();

        $categories = PrimaryCategory::with('secondary')
            ->get();

        return view('owner.products.edit', 
            compact(
                'product', 
                'quantity', 
                'shops',
                'images',
                'categories',
            ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\ProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $request->validate([
            'current_quantity' => 'required|integer',
        ]);

        $product = Product::findOrFail($id);
        // 在庫情報を取得
        $quantity = Stock::where('product_id', $product->id)
            ->sum('quantity');

        if($request->current_quantity !== $quantity) 
        {
            //編集している間に在庫数が変更されていたらフラッシュメッセージ
            $id = $request->route()->parameter('product'); 
            return redirect()
                ->route('owner.products.edit', ['product' => $id])
                ->with('message', '在庫数が変更されています。再度ご確認お願いします。');
        }
        else 
        { 
            try
            {
                DB::transaction(function () use($request, $product) {

                    $product->name = $request->name;
                    $product->information = $request->information;
                    $product->price = $request->price;
                    $product->sort_order = $request->sort_order;
                    $product->shop_id = $request->shop_id;
                    $product->secondary_category_id = $request->category;
                    $product->image1 = $request->image1;
                    $product->image2 = $request->image2;
                    $product->image3 = $request->image3;
                    $product->image4 = $request->image4;
                    $product->image5 = $request->image5;
                    $product->image6 = $request->image6;
                    $product->is_selling = $request->is_selling;
                    $product->save();

                    // 追加処理
                    if($request->type === \Constant::PRODUCT_LIST['add']) {
                        $newQuantity = $request->quantity;
                    } 
                    // 削減処理
                    if($request->type === \Constant::PRODUCT_LIST['reduce']) {
                        $newQuantity = $request->quantity * -1;
                    }

                    Stock::create([
                        'product_id' => $product->id,//作成したオーナーのIDを取得
                        'type' => $request->type,
                        'quantity' => $newQuantity
                    ]);
                //トランザクション２回繰り返し
                }, 2);
        
                return redirect()
                    ->route('owner.products.index')
                    ->with('message', '商品情報の更新が完了されました');
            }
            catch(\Throwable $e)
            {
                Log::error($e);
                throw $e;
            }
        }
            
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
