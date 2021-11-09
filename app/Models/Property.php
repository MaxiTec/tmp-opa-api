<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $fillable = ['name','manager','code','brand_img','address','phone','lat','lon','phone_code','rooms'];

    public function CriteriaByArea()
    {
        return $this->belongsToMany(AreaCriteria::class,'programs','property_id','area_criteria_id')->withTimestamps();
    }
}
