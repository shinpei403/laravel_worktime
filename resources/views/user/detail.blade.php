@extends('layout')
@section('title', '従業員詳細')
@section('content')
<div class="row">
    <div class="col-md-10 col-md-offset-2">
        <h2>id : {{ $user->id }} の従業員情報 詳細ページ</h2>
        </br>
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
                <th>登録日時</th>
                <td>{{ $user->created_at }}</td>
            </tr>
            <tr>
                <th>更新日時</th>
                <td>{{ $user->updated_at }}</td>
            </tr>
        </table>
        </br>
        <div>
            <a href="{{ route('userIndex') }}" class="link-primary">従業員一覧に戻る</a> 
        </div>
    </div>
</div>

@endsection