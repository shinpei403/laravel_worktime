<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'password',
        'role',
        'error_count',
        'locked_flg',
        'delete_flg',
        'created_at',
        'updated_at',
    ];

    public function worktime()
    {
        return $this->hasMany('App\Models\Worktime');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password', 'remember_token',
    // ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    /**
     * 従業員番号に一致する従業員を取得する
     * @param string $code
     * @return object $user
     */
    public function fetchUserByCode($code)
    {
        return User::where('code', '=', $code)->first();
    }

    /**
     * 従業員が存在しているか、削除されていないか(存在しなければtrueを返す)
     * @param object $user
     * @return bool 
     */
    public function hasUser($user)
    {
        return is_null($user) || $user->delete_flg === 1;
    }

    /**
     * 従業員を作成日の昇順で取得する
     * @param void
     * @return object $users
     */
    public function fetchUsersSortedByCreatedAt()
    {
        return User::orderBy('created_at')->paginate(10);
    }

    /**
     * 従業員をデータベースに登録する
     * @param array $inputs
     * @return bool 
     */
    public function storeUser($inputs)
    {
        
        \DB::beginTransaction();

        try{
            // パスワードをハッシュ化
            $inputs['password'] = Hash::make($inputs['password']);

            // 従業員登録  
            User::create($inputs);
            \DB::commit();
            return true;
        } catch(\Throwable $e){
            \DB::rollback();
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * IDに一致する従業員を取得する
     * @param int $id
     * @return object $user
     */
    public function fetchUserById($id)
    {
        return User::find($id);
    }

    /**
     * 対象の従業員のデータベースの情報を更新する
     * @param array $inputs
     * @return bool
     */
    public function updateUser($inputs)
    {
        \DB::beginTransaction();

        try{
            // 従業員を更新  
            $user = $this->fetchUserById($inputs['id']);

            $user->fill([
                'code' => $inputs['code'],
                'name' => $inputs['name'],
                'role' => $inputs['role'],
            ]);
            if($inputs['password']){
                $user->fill(['password' => Hash::make($inputs['password'])]);
            }
            $user->save();
            \DB::commit();
            return true;
        } catch(\Throwable $e){
            \DB::rollback();
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * 対象の従業員を論理削除する(delete_flg 0→1)
     * @param int $id
     * @return bool
     */
    public function deleteUserById($id)
    {
        \DB::beginTransaction();

        try{
            // 従業員を削除  
            $user = $this->fetchUserById($id);
            $user->delete_flg = 1;
            $user->save();
            \DB::commit();
            return true;
        } catch(\Throwable $e){
            \DB::rollback();
            Log::error($e->getMessage());
            return false;
        }
    }
}
