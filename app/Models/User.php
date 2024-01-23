<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasUuids;

   protected $table = 'users';
   protected $primaryKey = 'id';

   protected $fillable = [
    "email",
    "password",
    "firstname",
    "lastname",
    "password",
    "verification_code",
    "vCode_date"
   ];
 
}
