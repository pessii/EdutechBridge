<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadImageRequest;
use App\Models\Image;
use App\Services\ImageService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');
        //オーナーかの確認
        $this->middleware(function($request, $next){
            //imageのid取得 
            $id = $request->route()->parameter('image');
            if(!is_null($id)){
                $imagesOwnerId = Image::findOrFail($id)->owner->id;
                // キャスト 文字列→数値に型変換 
                $imageId = (int)$imagesOwnerId;
                // 同じでなかったら 
                if($imageId !== Auth::id()){ 
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
        $images = Image::where('owner_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        
        return view('owner.images.index',
            compact(
                'images'
            ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('owner.images.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\UploadImageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UploadImageRequest $request)
    {
        $imageFiles = $request->file('files');
        if(!is_null($imageFiles)){
            foreach($imageFiles as $imageFile){
                // 画像アップロード
                $fileNameToStore = ImageService::upload($imageFile, 'products');
                Image::create([
                    'owner_id' => Auth::id(),
                    'filename' => $fileNameToStore
                ]);
            }
        }

        return redirect()
            ->route('owner.images.index')
            ->with('message', '画像情報が登録されました');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $image = Image::findOrFail($id);
        
        return view('owner.images.edit',
            compact(
                'image'
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
        $request->validate([
            'title' => 'string|max:50',
        ]);

        // 保存処理
        $image = Image::findOrFail($id);
        $image->title = $request->title;
        $image->save();

        return redirect()
            ->route('owner.images.index')
            ->with('message', '画像情報が更新されました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        $filePath = 'public/products/' . $image->filename;

        Image::findOrFail($id)->delete();

        // ファイルパスがあったら削除
        if(Storage::exists($filePath)){
            Storage::delete($filePath);
        }

        return redirect()
            ->route('owner.images.index')
            ->with('message', '画像が削除されました');
    }
}
