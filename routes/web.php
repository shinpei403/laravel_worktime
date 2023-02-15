<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

//従業員一覧画面を表示
Route::get('/user/index', [UserController::class, 'showUserIndex'])->name('userIndex');