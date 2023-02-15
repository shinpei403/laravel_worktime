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

            return redirect(route('worktimeCreate'))->with('login_success', 'ログインしました。');
        }
        
        return back()->withErrors([
            'login_error' => '従業員番号かパスワードが間違っています。',
        ]);
    }
}
