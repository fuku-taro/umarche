<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use InterventionImage;

class ImageService
{
  public function upload($imageFile, $folderName)
  {
    if(is_array($imageFile)){
      $file = $imageFile['image'];
    } else {
      $file = $imageFile;
    }
    // リサイズを行う場合の処理
    // 一意のファイル名を生成（ランダムな文字列を元に）
    $fileName = uniqid(rand().'_');

    // 画像ファイルの拡張子を取得
    $extension = $file->extension();

    // 保存用のファイル名を生成（例: abc123.jpg）
    $fileNameToStore = $fileName . '.' . $extension;

    // Intervention Imageを使用して画像を1920x1080にリサイズし、エンコード
    $resizedImage = InterventionImage::make($file)->resize(1920, 1080)->encode();

    // リサイズ後の画像を指定したディレクトリに保存
    Storage::put('public/' . $folderName . '/' . $fileNameToStore, $resizedImage);

    return $fileNameToStore;
  }
}