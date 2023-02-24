<?php

namespace App\Http\Controllers\Worktime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Worktime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UpdateWorktimeRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use League\Csv\Writer;

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

    public function showWorktimeIndex(Request $request)
    {
        if(intval($request['month']) >= 1 && intval($request['month']) <= 12){
            $selectedMonth = intval($request['month']);
        }else{
            $selectedMonth = intval(Carbon::now()->format('m'));
        }
        
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = Carbon::create(null, $i, null, 0, 0, 0);
            $key = $month->format('m');
            $value = $month->format('m月');
            $months[$key] = $value;
        }
        
        if(auth()->user()->role === 'admin'){
            $worktimes = Worktime::whereYear('date', now()->year)
                        ->whereMonth('date', $selectedMonth)
                        ->orderBy('date', 'asc')
                        ->paginate(10);
        } else{
            $worktimes = Worktime::where('user_id', auth()->id())
                        ->whereYear('date', now()->year)
                        ->whereMonth('date', $selectedMonth)
                        ->orderBy('date', 'asc')
                        ->paginate(10);
        }
        
        return view('worktime.index', ['worktimes' => $worktimes, 'months' => $months, 'selectedMonth' => $selectedMonth]);
    }

    public function showWorktimeEdit($id)
    {
        $worktime = Worktime::find($id);
        if(is_null($worktime))
        {
            return redirect(route('worktimeIndex'))->with('danger', 'データがありません。');

        } 

        return view('worktime.edit', ['worktime' => $worktime]);
    }

    public function exeWorktimeUpdate(UpdateWorktimeRequest $request)
    {
        $inputs = $request->all();
        \DB::beginTransaction();
        try{
            // 従業員を更新  
            $worktime = Worktime::find($inputs['id']);
            $worktime->fill([
                'start_time' => $inputs['start_time'],
                'end_time' => $inputs['end_time'],
                'working_flg' => 0,
            ]);
            $worktime->save();
            \DB::commit();
        } catch(\Throwable $e){
            \DB::rollback();
            Log::error($e->getMessage());
            abort(500);
        }

        return redirect(route('worktimeIndex'))->with('success', '更新が完了しました。');
    }

    public function exeWorktimeCsvDetail(Request $request)
    {
        if(intval($request['month']) >= 1 && intval($request['month']) <= 12){

            $month = intval($request['month']);

            $worktimes = DB::table('worktimes as W') 
                        ->select('W.date', 'U.code', 'U.name')
                        ->selectRaw('SEC_TO_TIME(
                                            CASE
                                                WHEN TIME_TO_SEC(TIMEDIFF(W.end_time, W.start_time)) >= 9*3600
                                                    THEN TIME_TO_SEC(TIMEDIFF(W.end_time, W.start_time)) - 3600
                                                ELSE
                                                    TIME_TO_SEC(TIMEDIFF(W.end_time, W.start_time))
                                            END
                                        ) as workingtime')
                        ->selectRaw('SEC_TO_TIME(
                                            CASE
                                                WHEN TIME_TO_SEC(TIMEDIFF(W.end_time, W.start_time)) >= 9*3600
                                                    THEN TIME_TO_SEC(TIMEDIFF(W.end_time, W.start_time)) - 32400
                                                ELSE
                                                    0
                                            END
                                        ) as overtime')
                        ->join('users as U', 'W.user_id', '=', 'U.id')
                        ->whereYear('W.date', now()->year)
                        ->whereMonth('W.date', $month)
                        ->orderBy('code', 'asc')
                        ->orderBy('date', 'asc')
                        ->get();
            
            //コレクションが空なら戻す
            if($worktimes->isEmpty()){
                return redirect(route('worktimeIndex'))->with('danger', 'データがありません。');
            }

            // ヘッダーを設定する
            $header = ['出勤日', '従業員番号', '従業員名', '労働時間', '残業時間'];

            // 各行の設定をする
            $rows = [];

            foreach($worktimes as $worktime){
            
                $rows[] = [
                    $worktime->date,
                    $worktime->code,
                    $worktime->name,
                    $worktime->workingtime,
                    $worktime->overtime,
                ];
            }

            // CSVファイルを作成し、データを書き込む
            $csv = Writer::createFromString('');
            $csv->insertOne($header);
            $csv->insertAll($rows);
            $csvDate = $csv->getContent();
            $csvDate = mb_convert_encoding($csvDate, 'SJIS-win', 'UTF-8');
            
            // CSVをダウンロードするためのレスポンスを作成する
            $response = new Response($csvDate, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $month . '月の勤怠情報の一覧.csv'
            ]);
        
            return $response;
        }

        return redirect(route('worktimeIndex'));
    }

    public function exeWorktimeCsvTotal(Request $request)
    {
        if(intval($request['month']) >= 1 && intval($request['month']) <= 12){

            $month = intval($request['month']);

            $worktimes = DB::table('worktimes as W') 
                        ->select('U.code', 'U.name')
                        ->selectRaw('SEC_TO_TIME(SUM(
                                            CASE
                                                WHEN TIME_TO_SEC(TIMEDIFF(W.end_time, W.start_time)) >= 9*3600
                                                    THEN TIME_TO_SEC(TIMEDIFF(W.end_time, W.start_time)) - 3600
                                                ELSE
                                                    TIME_TO_SEC(TIMEDIFF(W.end_time, W.start_time))
                                            END
                                        )) as workingtime')
                        ->selectRaw('SEC_TO_TIME(SUM(
                                            CASE
                                                WHEN TIME_TO_SEC(TIMEDIFF(W.end_time, W.start_time)) >= 9*3600
                                                    THEN TIME_TO_SEC(TIMEDIFF(W.end_time, W.start_time)) - 32400
                                                ELSE
                                                    0
                                            END
                                        )) as overtime')
                        ->join('users as U', 'W.user_id', '=', 'U.id')
                        ->whereYear('W.date', now()->year)
                        ->whereMonth('W.date', $month)
                        ->groupBy('U.code', 'U.name')
                        ->orderBy('code', 'asc')
                        ->get();

            //コレクションが空なら戻す
            if($worktimes->isEmpty()){
                return redirect(route('worktimeIndex'))->with('danger', 'データがありません。');
            }

            // ヘッダーを設定する
            $header = ['従業員番号', '従業員名', '労働時間(合計)', '残業時間(合計)'];

            // 各行の設定をする
            $rows = [];

            foreach($worktimes as $worktime){
                
                $rows[] = [
                    $worktime->code,
                    $worktime->name,
                    $worktime->workingtime,
                    $worktime->overtime,
                ];

            }

            // CSVファイルを作成し、データを書き込む
            $csv = Writer::createFromString('');
            $csv->insertOne($header);
            $csv->insertAll($rows);
            $csvDate = $csv->getContent();
            $csvDate = mb_convert_encoding($csvDate, 'SJIS-win', 'UTF-8');
            
            // CSVをダウンロードするためのレスポンスを作成する
            $response = new Response($csvDate, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=' . $month . '月の勤怠情報の集計.csv'
            ]);
        
            return $response;
        }

        return redirect(route('worktimeIndex'));
    }
}
