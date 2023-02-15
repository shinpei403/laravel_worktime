<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    /**
     * 従業員一覧画面を表示する
     * @return view
     */
    public function showUserIndex()
    {
        $users = User::all();

        return view('user.index', ['users' => $users]);

    }

    /**
     * 従業員登録画面を表示する
     * @return view
     */
    public function showUserCreate()
    {
        
        return view('user.create');

    }

    /**
     * 従業員登録
     * @return view
     */
    public function exeUserStore(UserRequest $request)
    {
        $inputs = $request->all();
        \DB::beginTransaction();
        try{
            // 従業員登録  
            User::create($inputs);
            \DB::commit();
        } catch(\Throwable $e){
            \DB::rollback();
            abort(500);
        }
        
        return redirect(route('userIndex'))->with('success', '登録が完了しました。');
    }

    /**
     * 従業員詳細画面を表示する
     * @param int $id
     * @return view
     */
    public function showUserDetail($id)
    {
        $user = User::find($id);

        if(is_null($user) || $user->delete_flg === 1)
        {

        return redirect(route('userIndex'))->with('err_msg', 'データがありません。');
        
        }

        return view('user.detail', ['user' => $user]);

    }
}
