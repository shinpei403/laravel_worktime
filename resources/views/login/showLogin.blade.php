@extends('layout')
@section('title', 'ログインフォーム')
@section('content')
<div class="row">
    <div class="col-md-6 offset-3">
      <h2 class="mb-3">ログインフォーム</h2>
      
      <x-alert type="success" :session="session('success')" />
      <x-alert type="danger" :session="session('danger')" />

      <form method="POST" action="{{ route('Login') }}">
        @csrf
        <div class="form-group">
          <label for="code">従業員番号</label>
          <input type="text" class="form-control" name="code" value="{{ old('code') }}">  
        </div>  
        @if ($errors->has('code'))
          <div class="text-danger mb-3">
            {{ $errors->first('code') }}
          </div>
        @endif
        <div class="form-group">
          <label for="password">パスワード</label>
          <input type="password" class="form-control" name="password" value="{{ old('password') }}">
        </div>
        @if ($errors->has('password'))
          <div class="text-danger mb-3">
            {{ $errors->first('password') }}
          </div>
        @endif
        <button type="submit" class="btn btn-primary">ログイン</button>
      </form>
    </div>
</div>
@endsection
