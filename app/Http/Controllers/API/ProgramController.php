<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ProgramController extends Controller
{
    public function index(Request $request){
        // Mostramos al administrador todas el catalogo de Secciones, Areas y poreguntas

        // Accedemos con Query Builders para mas Facilidad en los filtros
        $users = DB::table('programs')->get();
        return $users;
    }

    public function store(Request $request){
        // Mostramos al administrador todas el catalogo de Secciones, Areas y poreguntas

        // Accedemos con Query Builders para mas Facilidad en los filtros
        $users = DB::table('programs')->get();
        return $users;
    }
}
