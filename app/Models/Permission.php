<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    // Is necessary for use it in API routes accept all guards (api, web, etc.)
    protected $guard_name = '*';
}
