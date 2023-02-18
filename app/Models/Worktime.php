<?php

namespace App\Models\Worktime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Woritime extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'break_time',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
