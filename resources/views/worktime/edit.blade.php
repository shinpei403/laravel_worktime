@extends('layout')
@section('title', '勤怠編集')
@section('content')
  <div class="row">
    <div class="col-md-8 offset-2">
        <h2 class="mb-3">{{ $worktime->user->name }} ({{ $worktime->date }})の勤退情報 編集</h2>
      
        <form method="POST" action="{{ route('worktimeUpdate') }}">
          @csrf
          <div class="form-group">
            <label for="start_time">出勤時間</label>
            <input type="time" name="start_time" class="form-control col-md-2" value = "{{ $worktime->start_time }}">
          </div>
          @if ($errors->has('start_time'))
            <div class="text-danger mb-3">
              {{ $errors->first('start_time') }}
            </div>
          @endif
          <div class="form-group">
            <label for="end_time">退勤時間</label>
            <input type="time" name="end_time" class="form-control col-md-2" value = "{{ $worktime->end_time }}">
          </div>
          @if ($errors->has('end_time'))
            <div class="text-danger mb-3">
              {{ $errors->first('end_time') }}
            </div>
          @endif
            
          <input type="hidden" name="id" value="{{ $worktime->id }}">
          <button type="submit" class="btn btn-primary mb-3">更新</button>
        </form>
        <a href="{{ route('worktimeIndex') }}" class="link-primary">勤怠一覧に戻る</a>
    </div>
  </div>
@endsection