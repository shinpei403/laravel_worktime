@extends('layout')
@section('title', '勤怠登録')
@section('content')
  
<h2>勤怠登録</h2>
</br>

<x-alert type="success" :session="session('success')" />
  
@endsection