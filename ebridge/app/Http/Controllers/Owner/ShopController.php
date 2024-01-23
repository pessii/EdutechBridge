<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Http\Requests\UploadImageRequest;
use App\Services\ImageService;

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
     * @param  \Illuminate\Http\UploadImageRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UploadImageRequest $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'information' => 'required|string|max:1000',
            'is_selling' => 'required',
        ]);

        //一時保存 
        $imageFile = $request->image;
        if(!is_null($imageFile) && $imageFile->isValid() ){
            // オーナー画像アップロード
            $fileNameToStore = ImageService::upload($imageFile, 'shops');
        }

        // 保存処理
        $shop = Shop::findOrFail($id);
        $shop->name = $request->name;
        $shop->information = $request->information;
        $shop->is_selling = $request->is_selling;
        if(!is_null($imageFile) && $imageFile->isValid())
        {
            $shop->filename = $fileNameToStore;
        }

        $shop->save();

        return redirect()
            ->route('owner.shops.index')
            ->with('message', '店舗情報が更新されました');
    }
}
