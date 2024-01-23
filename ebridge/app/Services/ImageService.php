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

        Storage::put('public/' . $folderName . '/' . $fileNameToStore, $resizedImage);

        return $fileNameToStore;
    }
}