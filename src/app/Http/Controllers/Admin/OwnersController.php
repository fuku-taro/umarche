<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OwnerUpdateRequest;
use App\Models\Owner; // エロクアント
use App\Models\Shop; // エロクアント
use Illuminate\Support\Facades\DB; // クエリビルダ
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Throwable;

class OwnersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        // $date_now = Carbon::now();
        // $date_parse = Carbon::parse(now());
        // echo $date_now->year;
        // echo '<br>';
        // echo $date_parse;
        // echo '<br>';

        // $e_all = Owner::all();
        // $q_get = DB::table('owners')->select('name', 'created_at')->get();
        // $q_first = DB::table('owners')->select('name', 'crated_at')->first();

        // $c_test = collect([
        //     'name' => 'テスト'
        // ]);

        // var_dump($q_first);
        // dd($e_all, $q_get, $q_first, $c_test);

        $owners = Owner::select('id', 'name', 'email', 'created_at')
        ->paginate(10);

        return view('admin.owners.index',
        compact('owners')
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.owners.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OwnerUpdateRequest $request)
    {
        try{
            DB::transaction(function() use($request){
                $owner = Owner::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                Shop::create([
                    'owner_id' => $owner->id,
                    'name' => '店名を入力してください',
                    'information' => '',
                    'filename' => '',
                    'is_selling' => true,
                ]);
            }, 2); //試行回数
        }catch(Throwable $e){
            Log::error($e);
            throw $e;
        }
        
        return redirect()
        ->route('admin.owners.index')
        ->with([
            'message' => 'オーナー情報を登録しました',
            'status' => 'info'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $owner = Owner::findOrFail($id);
        
        return view('admin.owners.edit', compact('owner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OwnerUpdateRequest $request, $id)
    {
        // $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Owner::class],
        //     'password' => ['required', 'confirmed', Rules\Password::defaults()],
        // ]);

        $owner = Owner::findOrFail($id);

        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->password = Hash::make($request->password);
        $owner->save();

        return redirect()
        ->route('admin.owners.index')
        ->with([
            'message' => 'オーナー情報を更新しました',
            'status' => 'info'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Owner::findOrFail($id)->delete(); //ソフトデリート

        return redirect()
        ->route('admin.owners.index')
        ->with([
            'message' => 'オーナー情報を削除しました',
            'status' => 'alert'
        ]);
    }

    public function expiredOwnerIndex()
    {
        $expiredOwners = Owner::onlyTrashed()->get();

        return view('admin.expired-owners',
        compact('expiredOwners'));
    }

    public function expiredOwnerDestroy($id)
    {
        Owner::onlyTrashed()->findOrFail($id)->forceDelete();

        return redirect()
        ->route('admin.expired-owners.index')
        ->with([
            'message' => 'オーナー情報を完全に削除しました',
            'status' => 'alert'
        ]);
    }
}
