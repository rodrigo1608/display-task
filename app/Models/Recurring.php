<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurring extends Model
{
    use HasFactory;

    // protected $table = 'recurrings';

    protected $fillable = [

        'sunday',

        'monday',

        'tuesday',

        'wednesday',

        'thursday',

        'friday',

        'saturday',

        'specific_date',

        'reminder_id',

    ];

    public function reminder()
    {
        return $this->belongsTo(Reminder::class, 'reminder_id');
    }
}
