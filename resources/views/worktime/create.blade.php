@extends('layout')
@section('title', '勤怠登録')
@section('content')
<div class="row">
    <div class="col-md-6 offset-3">
      <h2>勤怠登録</h2>

      @if(isset($worktime->working_flg) && $worktime->working_flg === 0)
        <div class="alert alert-warning" role="alert">
            本日は既に出退勤が登録されています
        </div>
      @endif

      <x-alert type="success" :session="session('success')" />
      
      <form id="worktime-form" method="POST" action="{{ route('worktimeStore') }}">
          @csrf
          <div class="form-group row">
              <label for="start_time" class="col-md-4 col-form-label text-md-right">出勤時間</label>
              <div class="col-md-6">
                @if(isset($worktime->working_flg) && ($worktime->working_flg === 1 || $worktime->working_flg === 0))
                    <button id="start-button" type="button" class="btn btn-primary" disabled>出勤する</button>
                @else
                    <button id="start-button" type="button" class="btn btn-primary">出勤する</button>
                @endif
              <input id="start_time" type="hidden" name="start_time">
              </div>
          </div>
          <div class="form-group row">
              <label for="end_time" class="col-md-4 col-form-label text-md-right">退勤時間</label>
              <div class="col-md-6">
                @if(isset($worktime->working_flg) && $worktime->working_flg === 1)
                  <button id="end-button" type="button" class="btn btn-secondary">退勤する</button>
                @else
                  <button id="end-button" type="button" class="btn btn-secondary" disabled>退勤する</button>
                @endif
                  <input id="end_time" type="hidden" name="end_time">
              </div>
          </div>
          <div class="form-group row mb-0">
              <div class="col-md-6 offset-md-4">
                  <button id="submit-button" type="submit" class="btn btn-primary" disabled>登録する</button>
                  <input type='hidden' name='user_id' value="{{ Auth::user()->id }}">
              </div>
          </div>
      </form>
    </div>
</div>

<script>
    const startButton = document.getElementById('start-button');
    const endButton = document.getElementById('end-button');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const submitButton = document.getElementById('submit-button');

    // 出勤ボタンがクリックされたときの処理
    startButton.addEventListener('click', () => {
        const now = new Date();
        const startTime = new Date(0, 0, 0, now.getHours(), now.getMinutes());
        startTimeInput.value = startTime.toLocaleTimeString();
        startButton.disabled = true;
        submitButton.disabled = false;
    });

    // 退勤ボタンがクリックされたときの処理
    endButton.addEventListener('click', () => {
        const now = new Date();
        const endTime = new Date(0, 0, 0, now.getHours(), now.getMinutes());
        endTimeInput.value = endTime.toLocaleTimeString();
        endButton.disabled = true;
        submitButton.disabled = false;
    });
</script>
@endsection