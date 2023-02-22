@extends('layout')
@section('title', '勤怠一覧')
@section('content')
  <div class="row">
    <div class="col-md-12">
        <h2>勤怠一覧</h2>
        </br>

        <x-alert type="success" :session="session('success')" />

        <x-alert type="danger" :session="session('danger')" />
        
        <table class="table table-striped">
            <tr>
                <th>出勤日</th>
                <th>従業員番号</th>
                <th>従業員名</th>
                <th>出勤時間</th>
                <th>退勤時間</th>
                <th>残業時間(分)</th>
                @if(Auth::user()->role === 'admin')
                  <th></th>
                @endif
            </tr>
            @foreach($worktimes as $worktime)
            <tr>
                <td>{{ $worktime->date }}</td>
                <td>{{ $worktime->user->code }}</td>
                <td>{{ $worktime->user->name }}</td>
                <td>{{ Carbon\Carbon::parse($worktime->start_time)->format('H:i'); }}</td>
                @if (is_null($worktime->end_time))
                  <td>(就業中)</td>
                  <td></td>
                @else
                  <td>{{ Carbon\Carbon::parse($worktime->end_time)->format('H:i'); }}</td>
                  <!-- 休憩時間は1時間とする -->
                  <td>{{ max(Carbon\Carbon::parse($worktime->end_time)->diffInMinutes(Carbon\Carbon::parse($worktime->start_time)) - 9*60, 0) }}分</td> 
                @endif
                @if(Auth::user()->role === 'admin')
                  <td>
                    <button type="button" class="btn btn-primary" onclick="location.href='{{ route('worktimeEdit', ['id' => $worktime->id]) }}'">編集</button>
                  </td>
                @endif
            </tr>
            @endforeach
        </table>
        {{ $worktimes->links() }}
        <a href="{{ route('worktimeCreate') }}" class="link-primary">勤怠登録画面に戻る</a>
    </div>
  </div>
@endsection