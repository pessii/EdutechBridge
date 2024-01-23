<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shop;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use InterventionImage;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');
        //オーナーかの確認
        $this->middleware(function($request, $next){
            //shopのid取得 
            $id = $request->route()->parameter('shop');
            if(!is_null($id)){
                $shopsOwnerId = Shop::findOrFail($id)->owner->id;
                // キャスト 文字列→数値に型変換 
                $shopId = (int)$shopsOwnerId;
                $ownerId = Auth::id();
                // 同じでなかったら 
                if($shopId !== $ownerId){ 
                    abort(404); // 404画面表示 
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
        $shops = Shop::where('owner_id', Auth::id())->get();
        
        return view('owner.shops.index',
            compact(
                'shops'
            ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shop = Shop::findOrFail($id);
        
        return view('owner.shops.edit',
            compact(
                'shop'
            ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //一時保存 
        $imageFile = $request->image;
        if(!is_null($imageFile) && $imageFile->isValid() ){
            // (リサイズなし) 
            // ファイル名の一意のIDを自動的に生成して保存
            // Storage::putFile('public/shops', $imageFile); 

            // (リサイズあり)
            // ランダムなファイル名を作成
            $fileName = uniqid(rand().'_'); 
            // 拡張子を取得
            $extension = $imageFile->extension(); 
            $fileNameToStore = $fileName. '.' . $extension; 
            // リサイズ処理
            $resizedImage = InterventionImage::make($imageFile)->resize(1920, 1080)->encode();

            Storage::put('public/shops/' . $fileNameToStore, $resizedImage);
        }

        return redirect()->route('owner.shops.index');
    }
}
