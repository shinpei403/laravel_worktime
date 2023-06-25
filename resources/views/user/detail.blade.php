@extends('layout')
@section('title', '従業員詳細')
@section('content')
<div class="row">
    <div class="col-md-8 offset-2">
        <h2 class="mb-3">id : {{ $user->id }} の従業員情報 詳細ページ</h2>

        <table class="table table-bordered">
            <tr>
                <th>従業員番号</th>
                <td>{{ $user->code }}</td>
            </tr>
            <tr>
                <th>従業員名</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>権限</th>
                @if ($user->role === 'admin')
                <td>管理者</td>
                @else
                <td>一般</td>
                @endif
            </tr>
            <tr>
                <th>登録日時</th>
                <td>{{ $user->created_at }}</td>
            </tr>
            <tr>
                <th>更新日時</th>
                <td>{{ $user->updated_at }}</td>
            </tr>
        </table>
        <div class="mb-2">
            <a href="{{ route('userEdit', ['id' => $user->id]) }}" class="link-primary">この従業員を編集する</a> 
        </div>
        <div>
            <a href="{{ route('userIndex') }}" class="link-primary">従業員一覧に戻る</a> 
        </div>
    </div>
</div>
@endsection