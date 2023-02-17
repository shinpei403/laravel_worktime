<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('worktimeCreate')->with('success', 'ログインしました。');
        }
        
        return back()->with('danger', '従業員番号かパスワードが間違っています。');
    }

    /**
     * ユーザーをアプリケーションからログアウトさせる
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exeLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('showLogin')->with('success', 'ログアウトしました。');
    }
}
