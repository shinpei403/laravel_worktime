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
    public function __construct(Worktime $worktime)
    {
        $this->worktime = $worktime;
    }
    
    /**
     * 勤怠登録画面を表示する
     * @param void
     * @return view, Worktime $worktime
     */
    public function showWorktimeCreate()
    {
        $worktime = $this->worktime->fetchWorktimeByToday();
    
        return view('worktime.create', ['worktime' => $worktime]);
    }

    /**
     * 勤怠情報を登録する
     * @param Request $request
     */
    public function exeWorktimeStore(Request $request)
    {
        $inputs = $request->all();
        $status = 'danger';
        $message = '登録に失敗しました。';

        if($this->worktime->storeWorktime($inputs)){

            $status = 'success';
            $message = '登録が完了しました。';

        }

        return redirect(route('worktimeCreate'))->with($status, $message);
    }

    /**
     * 勤怠情報一覧を表示する
     * @param Request $request
     * @return view, array $month, int $selectedMonth
     */
    public function showWorktimeIndex(Request $request)
    {
        // 月のパラメータを確認して、設定する
        $month = intval($request['month']);
        $selectedMonth = $this->worktime->checkMonth($month);
        
        // 月の配列を作製する
        $months = $this->worktime->createMonthArr();
        
        if(auth()->user()->role === 'admin'){

            // 管理者の場合は全従業員の勤怠情報を取得する
            $worktimes = $this->worktime->fetchAllWorktimes($selectedMonth);

        } else{

            // ログイン者の勤怠情報を取得する
            $worktimes = $this->worktime->fetchLoginUserWorktimes($selectedMonth);
        }
        
        return view('worktime.index', ['worktimes' => $worktimes, 'months' => $months, 'selectedMonth' => $selectedMonth]);
    }

    /**
     * 勤怠情報編集画面を表示する
     * @param int $id
     * @return view, Worktime $worktime
     */
    public function showWorktimeEdit($id)
    {
        $worktime = $this->worktime->fetchWorktimeById($id);

        if(is_null($worktime)){
            return redirect(route('worktimeIndex'))->with('danger', 'データがありません。');

        } 

        return view('worktime.edit', ['worktime' => $worktime]);
    }

    /**
     * 勤怠情報を更新する
     * @param UpdateWorktimeRequest $request
     */
    public function exeWorktimeUpdate(UpdateWorktimeRequest $request)
    {
        $inputs = $request->all();
        $status = 'danger';
        $message = '更新に失敗しました。';

        if($this->worktime->updateWorktime($inputs)){

            $status = 'success';
            $message = '更新が完了しました。';

        }
        
        return redirect(route('worktimeIndex'))->with($status, $message);
    }

    // ここから
    /**
     * CSVで勤怠情報一覧を出力する
     * @param Request $request
     * @return Response $response
     */
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

        return redirect(route('worktimeIndex'))->with('danger', 'データがありません。');
    }

    /**
     * CSVで勤怠情報集計を出力する
     * @param Request $request
     * @return Response $response
     */
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

        return redirect(route('worktimeIndex'))->with('danger', 'データがありません。');
    }
}
