<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [

        'title',

        'local',

        'concluded',

        'available',

        'created_by',

    ];

    // Função de relacionamento
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function durations()
    {
        return $this->hasMany(Duration::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants')->withPivot('status')
            ->withTimestamps();
    }

    public function reminder()
    {
        return $this->hasOne(Reminder::class);
    }
}
