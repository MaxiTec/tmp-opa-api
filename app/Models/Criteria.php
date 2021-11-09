<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    protected $table = 'criteria';

    public function areas()
    {
        return $this->belongsToMany(Area::class);
    }
}
