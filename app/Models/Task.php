<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'criteria',
        'status',
        'company_data',
        'notified_at',

    ];

    protected $casts = [
        'notified_at' => 'datetime'
    ];

    const STATUS_RECEIVED = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED = 3;

    public function getStatusAttribute($value)
    {
       return $this->getTaskStatuses()[$value];
    }

    public function getTaskStatuses()
    {
        return [
            self::STATUS_RECEIVED  => 'Received',
            self::STATUS_IN_PROGRESS => 'In progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_FAILED    => 'Failed',
        ];
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
