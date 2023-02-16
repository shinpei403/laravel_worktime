@extends('layout')
@section('title', 'ログインフォーム')
@section('content')
<div class="row">
    <div class="col-md-6 offset-3">
      <h2>ログインフォーム</h2>
      @foreach ($errors->all() as $error)
        <ul class="alert alert-danger">
          <li>{{$error}}</li>
        </ul>
      @endforeach
      @if (session('logout'))
          <div class="alert alert-success">
              {{ session('logout') }}
          </div>
      @endif
      <form method="POST" action="{{ route('Login') }}">
        @csrf
        <div class="form-group">
          <label for="code">従業員番号</label>
          <input type="text" class="form-control" name="code" value="{{ old('code') }}">
          </br>
          <label for="password">パスワード</label>
          <input type="password" class="form-control" name="password" value="{{ old('password') }}">
        </div>
        <button type="submit" class="btn btn-primary">ログイン</button>
      </form>
    </div>
</div>
@endsection
