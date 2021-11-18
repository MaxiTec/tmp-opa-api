<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;
    protected $table = 'audits';
    protected $fillable = ['area_criteria_id','check','not_apply','observations'];

    public function programs()
    {
        return $this->belongsToMany(Program::class,'audit_programs','programs_id','audits_id')->withPivot('id');
        // return $this->belongsToMany(Program::class,'audit_programs','programs_id','audits_id')->withPivot('id','user_id','expiry_date','executed_date','deleted_date','admin_id','is_active');
    }
}
