<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function section(){
        return $this->belongsTo('App\Models\Section');
    }
    public function criteria(){
        // return $this->belongsToMany(Criteria::class);
        return $this->belongsToMany(Criteria::class, 'area_criteria', 'area_id','criteria_id')->withPivot('id');
    }
    public function scopeActive($query){
        return $query->where('status', true);
    }
}
