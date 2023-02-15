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
  
@endsection