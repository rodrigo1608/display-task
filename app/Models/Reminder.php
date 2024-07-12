<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [

        'title',

        'notification_message',

        'available',

        'user_id',

        'task_id'

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function recurring()
    {
        return $this->hasOne(Recurring::class);
    }

    public function notificationTimes()
    {
        return $this->hasMany(NotificationTime::class);
    }
}
