<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'role',
        'color',
        'profile_picture',
        'email',
        'password',
        'telephone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',

            'password' => 'hashed',
        ];
    }

    // Relacionamento

    public function durations()
    {
        return $this->hasMany(Duration::class);
    }

    public function reminder()
    {
        return $this->hasOne(Reminder::class);
    }

    public function task()
    {
        return $this->hasMany(Task::class, 'created_by')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function participatingTasks()
    {
        return $this->belongsToMany(Task::class, 'participants')
            ->withPivot('status')
            ->withTimestamps();
    }
}
