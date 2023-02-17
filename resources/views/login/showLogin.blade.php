@extends('layout')
@section('title', 'ログインフォーム')
@section('content')
<div class="row">
    <div class="col-md-6 offset-3">
      <h2>ログインフォーム</h2>
      
      <x-alert type="success" :session="session('success')" />
      <x-alert type="danger" :session="session('danger')" />

      <form method="POST" action="{{ route('Login') }}">
        @csrf
        <div class="form-group">
          <label for="code">従業員番号</label>
          <input type="text" class="form-control" name="code" value="{{ old('code') }}">
          @if ($errors->has('code'))
            <div class="text-danger">
              {{ $errors->first('code') }}
            </div>
          @endif
          </br>
          <label for="password">パスワード</label>
          <input type="password" class="form-control" name="password" value="{{ old('password') }}">
          @if ($errors->has('password'))
            <div class="text-danger">
              {{ $errors->first('password') }}
            </div>
          @endif
        </div>
        <button type="submit" class="btn btn-primary">ログイン</button>
      </form>
    </div>
</div>
@endsection
