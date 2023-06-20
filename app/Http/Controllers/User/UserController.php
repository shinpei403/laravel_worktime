<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UpdateUserRequest;


class UserController extends Controller
{

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * 従業員一覧画面を表示する
     * @param void
     * @return view
     */
    public function showUserIndex()
    {

        $users = $this->user->fetchUsersSortedByCreatedAt();

        return view('user.index', ['users' => $users]);

    }

    /**
     * 従業員登録画面を表示する
     * @param void
     * @return view
     */
    public function showUserCreate()
    {
        
        return view('user.create');

    }

    /**
     * 従業員登録
     * @param UserRequest $request
     * @return view
     */
    public function exeUserStore(UserRequest $request)
    {
        $inputs = $request->all();
        $status = 'danger';
        $message = '登録に失敗しました。';
        
        if($this->user->storeUser($inputs)){

            $status = 'success';
            $message = '登録が完了しました。';

        }
        
        return redirect(route('userIndex'))->with($status, $message);
        
    }

    /**
     * 従業員詳細画面を表示する
     * @param int $id
     * @return view
     */
    public function showUserDetail($id)
    {
        $user = $this->user->fetchUserById($id);

        if($this->user->hasUser($user)){

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
        $user = $this->user->fetchUserById($id);

        if($this->user->hasUser($user)){

            return redirect(route('userIndex'))->with('danger', 'データがありません。');

        } 

        return view('user.edit', ['user' => $user]);

    }

    /**
     * 従業員更新
     * @param UpdateUserRequest $request
     * @return view
     */
    public function exeUserUpdate(UpdateUserRequest $request)
    {
        $inputs = $request->all();
        $status = 'danger';
        $message = '更新に失敗しました。';

        if($this->user->updateUser($inputs)){
            $status = 'success';
            $message = '更新が完了しました。';
        }

        return redirect(route('userIndex'))->with($status, $message);
    }

    /**
     * 従業員削除
     * @param int $id
     * @return view
     */
    public function exeUserDelete($id)
    {
        $status = 'danger';
        $message = '削除に失敗しました。';

        if($this->user->deleteUserById($id)){
            $status = 'success';
            $message = '削除が完了しました。';
        }

        return redirect(route('userIndex'))->with($status, $message);
    }
}
