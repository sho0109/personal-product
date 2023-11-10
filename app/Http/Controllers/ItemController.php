<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 商品一覧
     */
    public function index()
    {
        // 商品一覧取得
        $items = Item::all();

        return view('item.index', compact('items'));
    }


    /**
     * 検索
     */
    public function search(Request $request)
    {
        /* テーブルから全てのレコードを取得する */
        $query = Item::query();
        // 検索キーワード取得
        $keyword = $request -> input('keyword');
        // 検索結果取得
        if(!empty($query)){
            $query
            ->where('name', 'LIKE', "%$keyword%")
            ->orWhere('type', 'LIKE', "%$keyword%")
            ->orWhere('detail', 'LIKE', "%$keyword%");
        }
        $items = $query ->get();
        return view('item.index', compact('items'));
    }

    /**
     * 商品登録
     */
    public function add(Request $request)
    {
        // POSTリクエストのとき
        if ($request->isMethod('post')) {
            // バリデーション
            $this->validate($request, [
                'name' => 'required|max:100',
            ]);

            // 商品登録
            Item::create([
                'user_id' => Auth::user()->id,
                'name' => $request->name,
                'type' => $request->type,
                'detail' => $request->detail,
            ]);

            return redirect('/items');
        }

        return view('item.add');
    }


    /**
     * 商品情報編集
     * 
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit (Request $request, $id){
        //PUTリクエストの時->更新
        if($request->isMethod('put')){
            // バリデーション
            $this->validate($request,[
                'name'=>'required|max:100',
            ]);

            //商品情報更新
            $item = Item::find($id);
            $item -> update([
                'name' => $request -> name,
                'type' => $request -> type,
                'detail' => $request -> detail,
            ]);

            return redirect('/items');
        }

        //DELETEリクエストの時->削除
        if($request->isMethod('delete')){
            $item = Item::find($id);
            $item -> delete();
            return redirect('/items');
        }
        $item = Item::find($id);
        return view('item.edit',['item'=>$item,]);
    }
}
