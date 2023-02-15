@extends('layout')
@section('title', '従業員一覧')
@section('content')
  <div class="row">
    <div class="col-md-10 col-md-offset-2">
        <h2>従業員一覧</h2>
        </br>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('err_msg'))
            <div class="alert alert-danger">
                {{ session('err_msg') }}
            </div>
        @endif
        <table class="table table-striped">
            <tr>
                <th>従業員番号</th>
                <th>従業員名</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->code }}</td>
                <td>{{ $user->name }}</td>
                @if($user->delete_flg === 1)
                  <td>(削除済み)</td>
                  <td></td>
                  <td></td>
                @else
                <td>
                    <button type="button" class="btn btn-primary" onclick="location.href='{{ route('userDetail', ['id' => $user->id]) }}'">詳細</button>
                </td>
                <td>
                    
                </td>
                <td>
                    
                </td>
                @endif
            </tr>
            @endforeach
        </table>
        <a href="{{ route('userCreate') }}" class="link-primary">新規従業員の登録</a>
    </div>
  </div>
  <script>
  function checkDelete() {
    if (window.confirm('本当に削除してよろしいですか？')) {
        return true;
    } else{
        return false;
    }
  }
  </script>
@endsection