<?php

namespace App\Http\Controllers\Worktime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Worktime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WorktimeController extends Controller
{
    public function showWorktimeCreate()
    {
        $worktime = Worktime::where('date', Carbon::today()->toDateString())
                     ->where('user_id', auth()->id())
                     ->first();
        return view('worktime.create', ['worktime' => $worktime]);
    }

    public function exeWorktimeStore(Request $request)
    {
        $inputs = $request->all();
        \DB::beginTransaction();
        try{
            //出勤時間を登録
            if(!is_null($inputs['start_time'])){
                Worktime::create($inputs);
                \DB::commit();
            }
            //退勤時間を登録
            if(!is_null($inputs['end_time'])){
                $worktime = Worktime::where('date', Carbon::today()->toDateString())
                     ->where('user_id', auth()->id())
                     ->first();
                $worktime->fill([
                    'end_time' => $inputs['end_time'],
                    'working_flg' => 0, //退社のフラグを設定
                ]);
                $worktime->save();
                \DB::commit();
            }

        } catch(\Throwable $e){
            \DB::rollback();
            Log::error($e->getMessage());
            abort(500);
        }
        return redirect(route('worktimeCreate'))->with('success', '登録が完了しました。');

    }

    public function showWorktimeIndex()
    {
        $worktimes = Worktime::where('user_id', auth()->id())
                    ->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month)
                    ->orderBy('date', 'asc')
                    ->get();
        return view('worktime.index', ['worktimes' => $worktimes]);
    }

}
