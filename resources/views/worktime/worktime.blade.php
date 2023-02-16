@extends('layout')
@section('title', '勤怠登録')
@section('content')
  
<h2>勤怠登録</h2>
</br>
@if (session('login_success'))
    <div class="alert alert-success">
        {{ session('login_success') }}
    </div>
@endif

<form action="{{ route('Logout')  }}" method="POST">
    @csrf
    <button class="btn btn-danger">ログアウト</button>
</form>
  
@endsection