<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTime extends Model
{
    use HasFactory;

    protected $fillable = [

        'custom_time',

        'half_an_hour_before',

        'one_hour_before',

        'two_hours_before',

        'one_day_earlier',

        'user_id',

        'reminder_id'
    ];

    public function reminder()
    {
        return $this->belongsTo(Reminder::class, 'reminder_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
