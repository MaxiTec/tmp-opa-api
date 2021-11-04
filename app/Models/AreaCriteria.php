<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaCriteria extends Model
{
    use HasFactory;
    protected $fillable = ['area_id','criteria_id'];

    public function property()
    {
        return $this->belongsToMany(Property::class);
        // return $this->belongsToMany(Area::class, 'area_criteria', 'criteria_id', 'area_id');
    }
}
