<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
   protected $table = 'users';

   const LVL_INIT = 1;

   const LVL_ACTIVE = 2;

   const IS_ACTIVE = 1;
}
