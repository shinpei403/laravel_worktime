<nav class="navbar navbar-expand-sm navbar-dark bg-dark mt-0 mb-3">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav4" aria-controls="navbarNav4" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <a class="navbar-brand" href="{{ route('worktimeCreate') }}">勤怠管理システム</a>
        @auth
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('worktimeIndex') }}">勤怠一覧</a>
                </li>
                @if(Auth::user()->role === 'admin')
                    <li class="nav-item active">
                        <a class="nav-link" href="{{ route('userIndex') }}">従業員管理</a>
                    </li>
                @endif
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ Auth::user()->name }}<span class="caret"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <form id="logout-form" action="{{ route('Logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a>
                </div>
                </li>
            </ul>
        </div>
        @endauth
    </div>
</nav>
