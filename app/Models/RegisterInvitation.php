<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterInvitation extends Model
{
    protected $table = 'register_invitations';

    protected $fillable = ['email', 'token', 'expires_at'];


}
