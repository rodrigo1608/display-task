<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duration extends Model
{
    use HasFactory;

    protected $table = 'durations';

    protected $fillable = [
        'start',
        'end',
        'task_id',
        'user_id',
    ];

    /**
     * Get the task that owns the duration.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user that owns the duration.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
