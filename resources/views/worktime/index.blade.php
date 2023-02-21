@extends('layout')
@section('title', '勤怠一覧')
@section('content')
  <div class="row">
    <div class="col-md-12">
        <h2>勤怠一覧</h2>
        </br>
        <table class="table table-striped">
            <tr>
                <th>出勤日</th>
                <th>従業員番号</th>
                <th>従業員名</th>
                <th>出勤時間</th>
                <th>退勤時間</th>
                <th>残業時間(分)</th>
            </tr>
            @foreach($worktimes as $worktime)
            <tr>
                <td>{{ $worktime->date }}</td>
                <td>{{ $worktime->user->code }}</td>
                <td>{{ $worktime->user->name }}</td>
                <td>{{ $worktime->start_time }}</td>
                <td>{{ $worktime->end_time }}</td>
                <!-- 休憩時間は1時間とする -->
                <td>{{ max(Carbon\Carbon::parse($worktime->end_time)->diffInMinutes(Carbon\Carbon::parse($worktime->start_time)) - 9*60, 0) }}分</td> 
            </tr>
            @endforeach
        </table>
        <a href="{{ route('worktimeCreate') }}" class="link-primary">勤怠登録画面に戻る</a>
    </div>
  </div>
@endsection