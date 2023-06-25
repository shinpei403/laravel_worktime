<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * ログインフォームを表示する
     * @return view
     */
    public function showLogin()
    {
        return view('login.showLogin');
    }

    /**
     * ログインを行う
     * @return view
     */
    public function exeLogin(LoginRequest $request)
    {
        $credentials = $request->only('code', 'password');

        // 従業員番号に一致する従業員を取得する
        $user = $this->user->fetchUserByCode($credentials['code']);
        
        // 従業員が取得できなければ、ログイン画面に戻る
        if($this->user->hasUser($user)){
            return back()->with('danger', '従業員番号かパスワードが間違っています。');
        }

        // アカウントがロックされていないか確認する
        if($this->user->isAccountLocked($user->locked_flg)){
            return back()->with('danger', 'アカウントがロックされています。');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // ログインしたらエラーカウントをリセットする
            $this->user->resetErrorCount($user);

            return redirect()->route('worktimeCreate')->with('success', 'ログインしました。');
        }

        // ログイン失敗したらエラーカウントを1増やす
        $this->user->addErrorCount($user);

        // エラーカウントが5回になったら、アカウントをロックする
        if($this->user->lockAccount($user)){
            return back()->with('danger', 'アカウントがロックされました。管理者に連絡して下さい。');
        }

        return back()->with('danger', '従業員番号かパスワードが間違っています。');
    }

    /**
     * ユーザーをアプリケーションからログアウトさせる
     *
     * @param  Request  $request
     * @return Response
     */
    public function exeLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('showLogin')->with('success', 'ログアウトしました。');
    }
}
