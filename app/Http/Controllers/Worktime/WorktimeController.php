<?php

namespace App\Http\Controllers\Worktime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Worktime;
use App\Http\Requests\UpdateWorktimeRequest;


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

    /**
     * CSVで勤怠情報一覧を出力する
     * @param Request $request
     * @return Response $response
     */
    public function exeWorktimeCsv(Request $request)
    {
        $month = intval($request['month']);

        if($month >= 1 && $month <= 12){

            $fileType = $request['file'];

            if($fileType === 'detail'){

                // 明細表のデータを取得する
                $worktimes = $this->worktime->fetchWorktimesForDetailCsv($month);

            } else{
                
                // 集計表のデータを取得する
                $worktimes = $this->worktime->fetchWorktimesForTotalCsv($month);
            }
            

            //配列が空なら戻す
            if($worktimes->isEmpty()){
                return redirect(route('worktimeIndex'))->with('danger', 'データがありません。');
            }

            if($fileType === 'detail'){

                // CSVファイル(明細表)を作製する
                $csvData = $this->worktime->createDeteilCsvFile($worktimes);
                $str = '月の勤怠情報の一覧.csv';

            } else{
                
                // CSVファイル(集計表)を作製する
                $csvData = $this->worktime->createTotalCsvFile($worktimes);
                $str = '月の勤怠情報の集計.csv';
            }


            // CSVをダウンロードするためのレスポンスを作成する
            $response = response($csvData, 200) 
                        ->header('Content-Type', 'text/csv')
                        ->header('Content-Disposition', 'attachment; filename=' . $month . $str);
            
            return $response;
        }

        return redirect(route('worktimeIndex'))->with('danger', 'データがありません。');
    }
}
