<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;
    protected $table = 'audits';
    protected $fillable = ['user_id','programs_id','admin_id','is_active','is_visible','expiry_date','executed_date','deleted_date','observations'];

    // public function programs()
    // {
    //     return $this->belongsToMany(Program::class,'audit_programs','programs_id','audits_id')->withPivot('id');
    //     // return $this->belongsToMany(Program::class,'audit_programs','programs_id','audits_id')->withPivot('id','user_id','expiry_date','executed_date','deleted_date','admin_id','is_active');
    // }

    public function program()
    {
        return $this->belongsTo(Program::class,'programs_id');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class,'audits_questions','audits_id','questions_id')->withPivot('check','not_apply');;
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
