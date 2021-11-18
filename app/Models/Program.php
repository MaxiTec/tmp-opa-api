<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    protected $table = 'programs';

    public function audits()
    {
        return $this->belongsToMany(Audit::class,'audit_programs','audits_id','programs_id')->withPivot('id');
    }
}
