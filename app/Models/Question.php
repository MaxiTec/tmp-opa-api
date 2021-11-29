<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';
    protected $fillable = ['area_criteria_id'];
    
    public function areaCriteria()
    {
        return $this->hasMany(AreaCriteria::class, 'area_criteria_id', 'id');
    }
    
}
