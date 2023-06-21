<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

}
