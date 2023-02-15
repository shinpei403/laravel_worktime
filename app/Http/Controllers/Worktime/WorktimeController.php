<?php

namespace App\Http\Controllers\Worktime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorktimeController extends Controller
{
    public function showCreate()
    {
        return view('worktime.worktime');
    }
}
