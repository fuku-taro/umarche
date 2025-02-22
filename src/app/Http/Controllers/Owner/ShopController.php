<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UploadImageRequest;
use App\Models\Owner; // エロクアント
use App\Models\Shop; // エロクアント
use Illuminate\Support\Facades\DB; // クエリビルダ
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use InterventionImage;
use App\Services\ImageService;


class ShopController extends Controller
{
    private $imageServise;
    public function __construct(ImageService $imageServise)
    {
        $this->imageServise = $imageServise;
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

    public function update(UploadImageRequest $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'information' => ['required', 'string', 'max:1000'],
            'is_selling' => ['required'],
        ]);

        // リクエストから送信された画像ファイルを取得
        $imageFile = $request->image; // 一時保存

        // 画像ファイルが存在し、有効である場合に処理を実行
        if (!is_null($imageFile) && $imageFile->isValid()) {

            $fileNameToStore = $this->imageServise->upload($imageFile, 'shops');
            // リサイズを行わない場合の保存方法
            // Storage::putFile('public/shops', $imageFile);
        }

        $shop = Shop::findOrFail($id);

        $shop->name = $request->name;
        $shop->information = $request->information;
        $shop->is_selling = $request->is_selling;
        if (!is_null($imageFile) && $imageFile->isValid()) {
            $shop->filename = $fileNameToStore;
        }
        $shop->save();
        
        return redirect()->route('owner.shops.index')->with([
            'message' => '店舗情報を更新しました',
            'status' => 'info'
        ]);
    }
}
