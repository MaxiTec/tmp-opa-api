<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaCriteria extends Model
{
    use HasFactory;
    protected $table = 'area_criteria';
    protected $fillable = ['area_id','criteria_id'];

    public function property()
    {
        return $this->belongsToMany(Property::class)->withPivot('id');
    }

    // public function property()
    // {
    //     return $this->belongsToMany(Property::class);
    // }
    
}
