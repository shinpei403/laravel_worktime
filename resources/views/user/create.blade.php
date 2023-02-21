@extends('layout')
@section('title', '従業員登録')
@section('content')
  <div class="row">
    <div class="col-md-6 offset-3">
        <h2>従業員登録</h2>
        </br>
        <form method="POST" action="{{ route('userStore') }}">
          @csrf
          <div class="form-group">
            <label for="code">従業員番号</label>
            <input type="text" name="code" class="form-control" value = "{{ old('code') }}">
            @if ($errors->has('code'))
              <div class="text-danger">
                {{ $errors->first('code') }}
              </div>
            @endif
            </br>
            <label for="name">従業員名</label>
            <input type="text" name="name" class="form-control" value = "{{ old('name') }}">
            @if ($errors->has('name'))
              <div class="text-danger">
                {{ $errors->first('name') }}
              </div>
            @endif
            </br>
            <label for="passwoed">パスワード</label>
            <input type="password" name="password" class="form-control" value = "{{ old('password') }}">
            @if ($errors->has('password'))
              <div class="text-danger">
                {{ $errors->first('password') }}
              </div>
            @endif
            </br>
            <label for="role">権限</label>
            <select class="form-control col-md-3" name="role">
              <option value="">一般</option>
              <option value="admin" <?php if(old('role') === "admin") echo "selected"; ?>>管理者</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">登録</button>
        </form>
        </br>
        <a href="{{ route('userIndex') }}" class="link-primary">従業員一覧に戻る</a>
    </div>
  </div>
@endsection