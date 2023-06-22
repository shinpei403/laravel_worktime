<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;
use Illuminate\Support\Facades\Log;

class Worktime extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'working_flg',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * ログイン者の日付が今日の勤怠情報を取得する
     * @param void 
     * @return object $worktime
     */
    public function fetchWorktimeByToday()
    {

        return Worktime::where('date', Carbon::today()->toDateString())
                ->where('user_id', auth()->id())
                ->first();
    }

    /**
     * 勤怠情報を登録または更新する
     * @param array $inputs
     * @return bool
     */
    public function storeWorktime($inputs)
    {
        
        //出勤時間を登録する
        if(!is_null($inputs['start_time'])){

            return $this->storeStartTime($inputs);
        }

        //退勤時間を登録する
        if(!is_null($inputs['end_time'])){
            
            return $this->storeEndTime($inputs);
        }       
    }

    /**
     * 出勤時間を登録する
     * @param array $inputs
     * @return bool
     */
    public function storeStartTime($inputs)
    {
        \DB::beginTransaction();

        try{

            // 変数に今日の日付を格納する
            $today = Carbon::now()->toDateString();

            $inputs['date'] = $today;
            Worktime::create($inputs);
            \DB::commit();
            return true;

        } catch(\Throwable $e){
            \DB::rollback();
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * 退勤時間を登録する
     * @param array $inputs
     * @return bool
     */
    public function storeEndTime($inputs)
    {
        \DB::beginTransaction();

        try{

            // 変数に今日の日付を格納する
            $today = Carbon::today()->toDateString();
            
            // DBからログイン者の今日の勤怠情報を取得する
            $worktime = Worktime::where('date', $today)
                        ->where('user_id', auth()->id())
                        ->first();

            $worktime->fill([
                'end_time' => $inputs['end_time'],
                'working_flg' => 0, //勤怠フラグを設定(1→0)
            ]);

            $worktime->save();
            \DB::commit();
            return true;
        } catch(\Throwable $e){
            \DB::rollback();
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * パラメータの情報を確認して数値(月)を返す
     * @param string $month
     * @return int $month
     */
    public function checkMonth($month)
    {
        if($month >= 1 && $month <= 12){

            return $month;

        } else{

            $month = Carbon::now()->format('n');
            return $month;
        }
    }

    /**
     * 月の配列を作製して返す
     * @param void
     * @return array $months
     */
    public function createMonthArr()
    {
        for ($i = 1; $i <= 12; $i++) {
            $key = $i;
            $value = "{$i}月";
            $months[$key] = $value;
        }

        return $months;
    }

    /**
     * DBから全従業員の勤怠情報の一覧を取得する
     * @param int $selectedMonth
     * @return object $worktimes
     */
    public function fetchAllWorktimes($selectedMonth)
    {
        return Worktime::whereYear('date', now()->year)
               ->whereMonth('date', $selectedMonth)
               ->orderBy('date', 'asc')
               ->paginate(10);
    }

    /**
     * DBからログイン者の勤怠情報の一覧を取得する
     * @param int $selectedMonth
     * @return object $worktimes
     */public function fetchLoginUserWorktimes($selectedMonth)
     {
        return  Worktime::where('user_id', auth()->id())
                ->whereYear('date', now()->year)
                ->whereMonth('date', $selectedMonth)
                ->orderBy('date', 'asc')
                ->paginate(10);
     }

     /**
     * IDに一致する勤怠情報を取得する
     * @param int $id
     * @return object $worktime
     */
    public function fetchWorktimeById($id)
    {
        return Worktime::find($id);
    }

    /**
     * 勤怠情報を更新する
     * @param array $inputs
     * @return bool
     */
    public function updateWorktime($inputs)
    {
        \DB::beginTransaction();

        try{
            
            $worktime = $this->fetchWorktimeById($inputs['id']);

            $worktime->fill([
                'start_time' => $inputs['start_time'],
                'end_time' => $inputs['end_time'],
                'working_flg' => 0,
            ]);
            $worktime->save();
            \DB::commit();
            return true;
        } catch(\Throwable $e){
            \DB::rollback();
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * CSVに使う勤怠情報を取得する(明細表)
     * @param int $month
     * @return object $worktimes
     */
    public function fetchWorktimesForDetailCsv($month)
    {
        return DB::table('worktimes as W') 
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
    }

    /**
     * CSVファイル(明細表)を作製する
     * @param object $worktimes 
     * @return object $csvData
     */
    public function createDeteilCsvFile($worktimes)
    {
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
        $csv = Writer::createFromString();
        $csv->insertOne($header);
        $csv->insertAll($rows);
        $csvData = $csv->getContent();
        $csvData = mb_convert_encoding($csvData, 'SJIS-win', 'UTF-8');

        return $csvData;

    }

    /**
     * CSVに使う勤怠情報を取得する(集計表)
     * @param int $month
     * @return object $worktimes
     */
    public function fetchWorktimesForTotalCsv($month)
    {
        return DB::table('worktimes as W') 
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
    }

    /**
     * CSVファイル(集計表)を作製する
     * @param object $worktimes 
     * @return object $csvData
     */
    public function createTotalCsvFile($worktimes)
    {
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
        $csv = Writer::createFromString();
        $csv->insertOne($header);
        $csv->insertAll($rows);
        $csvData = $csv->getContent();
        $csvData = mb_convert_encoding($csvData, 'SJIS-win', 'UTF-8');

        return $csvData;

    }
}
