<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * 従業員一覧画面を表示する
     * @return view
     */
    public function showUserIndex()
    {
        $users = User::orderBy('created_at')->paginate(10);

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
            // パスワードをハッシュ化
            $inputs['password'] = Hash::make($inputs['password']);

            // 従業員登録  
            User::create($inputs);
            \DB::commit();
        } catch(\Throwable $e){
            \DB::rollback();
            Log::error($e->getMessage());
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

        return redirect(route('userIndex'))->with('danger', 'データがありません。');
        
        }

        return view('user.detail', ['user' => $user]);

    }

    /**
     * 従業員編集画面を表示する
     * @param int $id
     * @return view
     */
    public function showUserEdit($id)
    {
        $user = User::find($id);

        if(is_null($user) || $user->delete_flg === 1)
        {
            return redirect(route('userIndex'))->with('danger', 'データがありません。');

        } 

        return view('user.edit', ['user' => $user]);

    }

    /**
     * 従業員更新
     * @return view
     */
    public function exeUserUpdate(UpdateUserRequest $request)
    {
        $inputs = $request->all();
        \DB::beginTransaction();
        try{
            // 従業員を更新  
            $user = User::find($inputs['id']);
            $user->fill([
                'code' => $inputs['code'],
                'name' => $inputs['name'],
            ]);
            if($inputs['password']){
                $user->fill(['password' => Hash::make($inputs['password'])]);
            }
            $user->save();
            \DB::commit();
        } catch(\Throwable $e){
            \DB::rollback();
            Log::error($e->getMessage());
            abort(500);
        }
        
        return redirect(route('userIndex'))->with('success', '更新が完了しました。');
    }

    /**
     * 従業員削除
     * @param int $id
     * @return view
     */
    public function exeUserDelete($id)
    {
        try{
            // 従業員を削除  
            $user = User::find($id);
            $user->delete_flg = 1;
            $user->save();
            \DB::commit();
        } catch(\Throwable $e){
            \DB::rollback();
            Log::error($e->getMessage());
            abort(500);
        }

        return redirect(route('userIndex'))->with('success', '削除が完了しました。');

    }
}
