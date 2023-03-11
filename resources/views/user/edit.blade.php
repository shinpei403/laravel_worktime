@extends('layout')
@section('title', '従業員編集')
@section('content')
  <div class="row">
    <div class="col-md-6 offset-3">
        <h2 class="mb-3">id : {{ $user->id }} の従業員情報 編集</h2>
  
        <form method="POST" action="{{ route('userUpdate') }}">
          @csrf
          <div class="form-group">
            <label for="code">従業員番号</label>
            <input type="text" name="code" class="form-control" value = "{{ $user->code }}">
          </div>
          @if ($errors->has('code'))
            <div class="text-danger mb-3">
              {{ $errors->first('code') }}
            </div>
          @endif
          <div class="form-group">
            <label for="name">従業員名</label>
            <input type="text" name="name" class="form-control" value = "{{ $user->name }}">
          </div>
          @if ($errors->has('name'))
            <div class="text-danger mb-3">
              {{ $errors->first('name') }}
            </div>
          @endif
          <div class="form-group">
            <label for="passwoed">パスワード</label>
            <input type="password" name="password" class="form-control">
          </div>
          @if ($errors->has('password'))
            <div class="text-danger mb-3">
              {{ $errors->first('password') }}
            </div>
          @endif
          <div class="form-group">
            <label for="role">権限</label>
            <select class="form-control col-md-3" name="role">
              <option value="">一般</option>
              <option value="admin" <?php if($user->role === "admin") echo "selected"; ?>>管理者</option>
            </select>
          </div>
          <input type="hidden" name="id" value="{{ $user->id }}">
          <button type="submit" class="btn btn-primary mb-3">更新</button>
        </form>
        <a href="{{ route('userIndex') }}" class="link-primary">従業員一覧に戻る</a>
    </div>
  </div>
@endsection