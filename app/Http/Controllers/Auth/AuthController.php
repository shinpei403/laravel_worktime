<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
