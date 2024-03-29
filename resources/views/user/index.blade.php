@extends('layout')
@section('title', '従業員一覧')
@section('content')
  <div class="row">
    <div class="col-md-9 offset-1">
        <h2 class="mb-3">従業員一覧</h2>
        </br>
        
        <x-alert type="success" :session="session('success')" />

        <x-alert type="danger" :session="session('danger')" />

        <table class="table table-striped">
            <tr>
                <th>従業員番号</th>
                <th>従業員名</th>
                <th>
                    <i class="bi bi-lock-fill"></i>
                </th>
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
                @if($user->locked_flg === 1)
                    <td>
                        <form method="POST" action="{{ route('userUnlock', ['id' => $user->id]) }}">
                            @csrf         
                            <button type="submit" class="btn btn-primary">ロック解除</button>
                        </form>
                    </td>
                @else
                    <td></td>
                @endif
                <td>
                    <button type="button" class="btn btn-primary" onclick="location.href='{{ route('userDetail', ['id' => $user->id]) }}'">詳細</button>
                </td>
                <td>
                    <form method="POST" action="{{ route('userDelete', ['id' => $user->id]) }}" onSubmit="return checkDelete()">
                        @csrf         
                        <button type="submit" class="btn btn-danger">削除</button>
                    </form>
                </td>
                @endif
            </tr>
            @endforeach
        </table>
        <div class="d-flex justify-content-center">
            {{ $users->links() }}
        </div>
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