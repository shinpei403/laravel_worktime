<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Worktime\WorktimeController;

//ログイン後の画面
Route::middleware(['auth'])->group(function () {

  Route::middleware(['admin'])->group(function () {

      //従業員一覧画面を表示
      Route::get('/user/index', [UserController::class, 'showUserIndex'])->name('userIndex');
      
      // 従業員登録画面を表示
      Route::get('/user/create', [UserController::class, 'showUserCreate'])->name('userCreate');
      
      // 従業員登録
      Route::post('/user/store', [UserController::class, 'exeUserStore'])->name('userStore');
      
      // 従業員編集画面を表示
      Route::get('/user/edit/{id}', [UserController::class, 'showUserEdit'])->name('userEdit');
      
      // 従業員更新
      Route::post('/user/update', [UserController::class, 'exeUserUpdate'])->name('userUpdate');
      
      // 従業員削除
      Route::post('/user/delete/{id}', [UserController::class, 'exeUserDelete'])->name('userDelete');

      // ロック解除
      Route::post('/user/unlock/{id}', [UserController::class, 'exeUserUnlock'])->name('userUnlock');
      
      // 従業員詳細画面を表示
      Route::get('/user/{id}', [UserController::class, 'showUserDetail'])->name('userDetail');

      //勤怠編集画面を表示
      Route::get('worktime/edit/{id}', [WorktimeController::class, 'showWorktimeEdit'])->name('worktimeEdit');

      //勤怠更新
      Route::post('worktime/update', [WorktimeController::class, 'exeWorktimeUpdate'])->name('worktimeUpdate');

      //勤怠情報をCSVファイルでダウンロードする
      Route::get('worktime/csv', [WorktimeController::class, 'exeWorktimeCsv'])->name('worktimeCsv');

  });

  //勤怠登録画面を表示
  Route::get('worktime/create', [WorktimeController::class, 'showWorktimeCreate'])->name('worktimeCreate');

  //勤怠登録
  Route::post('worktime/store', [WorktimeController::class, 'exeWorktimeStore'])->name('worktimeStore');

  //勤怠一覧を表示
  Route::get('worktime/index', [WorktimeController::class, 'showWorktimeIndex'])->name('worktimeIndex');

  // ログアウト処理
  Route::post('login/logout', [AuthController::class, 'exeLogout'])->name('Logout');
  
});

//ログイン前の画面
Route::middleware(['guest'])->group(function () {

  //ログインフォームを表示
  Route::get('login/show', [AuthController::class, 'showLogin'])->name('showLogin');
  
  //ログイン処理
  Route::post('login/login', [AuthController::class, 'exeLogin'])->name('Login');
    
});

