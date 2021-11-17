<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;
     // Is necessary for use it in API routes accept all guards (api, web, etc.)
    protected $guard_name = '*';
}
