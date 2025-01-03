<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\OwnerUpdateRequest;
use App\Models\Owner; // エロクアント
use App\Models\Shop; // エロクアント
use Illuminate\Support\Facades\DB; // クエリビルダ
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use InterventionImage;


class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');
        
        $this->middleware(function ($request, $next) {
            $id = $request->route()->parameter('shop'); //shopのid取得
            if(!is_null($id)){ // nul判定
                $shopsOwnerId = Shop::findOrFail($id)->owner->id;
                $shopId = (int)$shopsOwnerId; // キャスト 文字列→数値に型変換
                $ownerId = Auth::id();
                if($shopId !== $ownerId){ // 同じでなかったら
                    abort(404); // 404画面表示
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        // phpinfo();
        $ownerId = Auth::id();
        $shops = Shop::where('owner_id', $ownerId)->get();

        return view('owner.shops.index',
        compact('shops')
        );
    }

    public function edit($id)
    {
        $shop = Shop::findOrFail($id);

        return view('owner.shops.edit', compact('shop'));
    }

    public function update(Request $request, $id)
    {
            // リクエストから送信された画像ファイルを取得
        $imageFile = $request->image; // 一時保存

        // 画像ファイルが存在し、有効である場合に処理を実行
        if (!is_null($imageFile) && $imageFile->isValid()) {

            // リサイズを行わない場合の保存方法
            // Storage::putFile('public/shops', $imageFile);

            // リサイズを行う場合の処理
            // 一意のファイル名を生成（ランダムな文字列を元に）
            $fileName = uniqid(rand().'_');

            // 画像ファイルの拡張子を取得
            $extension = $imageFile->extension();

            // 保存用のファイル名を生成（例: abc123.jpg）
            $fileNameToStore = $fileName . '.' . $extension;

            // Intervention Imageを使用して画像を1920x1080にリサイズし、エンコード
            $resizedImage = InterventionImage::make($imageFile)->resize(1920, 1080)->encode();

            // リサイズ後の画像を指定したディレクトリに保存
            Storage::put('public/shops/' . $fileNameToStore, $resizedImage);
        }

        return redirect()->route('owner.shops.index');
    }
}
