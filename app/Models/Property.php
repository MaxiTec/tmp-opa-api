<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $fillable = ['name','manager','code','brand_img','address','phone','lat','lon','phone_code','rooms'];


    public function CriteriosByArea()
    {
        return $this->belongsToMany(AreaCriteria::class);
        // return $this->belongsToMany(Area::class, 'area_criteria', 'criteria_id', 'area_id');
    }
}
