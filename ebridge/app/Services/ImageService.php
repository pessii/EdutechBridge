<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

use InterventionImage;

class ImageService
{
    /**
     * 画像アップロード
     *
     * @param [type] $imageFile
     * @param [type] $folderName
     * @return void
     */
    public static function upload($imageFile, $folderName)
    {
        if(is_array($imageFile)){
            $file = $imageFile['image'];
        } else {
            $file = $imageFile; 
        }
        // (リサイズあり)
        // ランダムなファイル名を作成
        $fileName = uniqid(rand().'_'); 
        // 拡張子を取得
        $extension = $file->extension(); 
        $fileNameToStore = $fileName. '.' . $extension; 
        // リサイズ処理
        $resizedImage = InterventionImage::make($file)->resize(1920, 1080)->encode();

        Storage::put('public/' . $folderName . '/' . $fileNameToStore, $resizedImage);

        return $fileNameToStore;
    }
}