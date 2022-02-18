<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $fillable = ['name','description'];

    public function areas(){
        // Solameentre podre ver los que no esten eliminados
        // puedo crear otra relacion para el amdin. supongo
        return $this->hasMany('App\Models\Area')->active();
    }
    // public function areasActive(){
    //     return $this->hasMany('App\Models\Area')->active();
    //     // return $this->posts()->published();
    // }
}
