<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

//従業員一覧画面を表示
Route::get('/user/index', [UserController::class, 'showUserIndex'])->name('userIndex');

// 従業員登録画面を表示
Route::get('/user/create', [UserController::class, 'showUserCreate'])->name('userCreate');

// 従業員登録
Route::post('/user/store', [UserController::class, 'exeUserStore'])->name('userStore');
