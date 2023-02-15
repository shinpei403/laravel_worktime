<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

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
}
