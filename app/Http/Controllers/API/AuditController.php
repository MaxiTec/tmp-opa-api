<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Audit;


use App\Models\Property;
use App\Models\Section;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Auth;
use Carbon\Carbon;
use DB;
// use App\Http\Resources\SectionCollection;
// use App\Http\Resources\SectionResource;
// use DB;

class AuditController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Audit::all();
        return $this->sendResponse($data, '');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $catalog = Section::with('areas','areas.criteria')->get();
        $userAdmin = auth()->user();
        // recibimos por Post el id del usuario y el id de la propiedad
        $user_id = $request->input('user_id'); 
        // Data of hotel
        $property = Property::find($id);

        // Saco todos los ids de Criteria Area (preguntas del hotel)
        // $criteria = $property->CriteriaByArea⁄;

        // Todas las preguntas que tiene asociado 
        $criteria = $property->CriteriaByArea->pluck('id');

        $programs = DB::table('programs as pr')
            ->join('properties as p', 'p.id', '=', 'pr.property_id')
            ->join('area_criteria as ac', 'pr.area_criteria_id', '=', 'ac.id')
            ->join('areas as a', 'a.id', '=', 'ac.area_id')
            ->join('sections as s', 's.id', '=', 'a.section_id')
            ->join('criteria as q', 'q.id', '=', 'a.section_id')
            ->select('q.id as question_id','pr.id as programs_id','ac.area_id', 'a.section_id', 'ac.id as criteria_id','s.name as section','a.name as area','q.name as question')
            ->where('pr.property_id',$id)
            ->get();
        $grouped = $programs->groupBy(['section','area']);
        // y hago una comparacion de todas las preguntas por secciones y areas y "checkeamos" los que tengan el hotel
        // return $catalog;
        // dd($user->id);
        // $questions = [];
        // return $userAdmin->id;
        // foreach ($catalog as $key1 => $section) {
        //     foreach ($section->areas as $key2 => $area) {
        //         $questions = [];
        //         // Aca guardaremos todos las preguntas que tendra cada sección

        //         foreach ($area->criteria as $key3 => $question) {
                    // if(in_array($question->pivot->id, $criteria->toArray())){
                    //     // $questions[$key1]['area_criteria_id'] = $question->pivot->criteria_id;
                    //     $auditoria = Audit::create(['area_criteria_id'=>$question->pivot->criteria_id]);
                    //     $data = [
                    //         'user_id' => $user_id,
                    //         'admin_id' => $userAdmin->id,
                    //         'executed_date' => Carbon::now()
                    //     ];
                    //     // $auditoria = new Audit(['area_criteria_id' => $question->pivot->criteria_id]);
                    //     $auditoria->programs()->attach($auditoria->id, $data);
                    // }
        //         }
        foreach ($grouped as $key => $section) {
            foreach ($section as $key2 => $area) {
                $questions = [];
                foreach ($area as $key3 => $question) {
                    $data = [
                        'user_id' => $user_id,
                        'admin_id' => $userAdmin->id,
                        'executed_date' => Carbon::now()
                    ];
                $auditoria = Audit::create(['area_criteria_id'=> $question->criteria_id]);
                $auditoria->programs()->attach($question->programs_id, $data);
                }
                
                
            }
            
        }
        // return 'TEST';
        return $questions;
        return sendResponse($auditoria, 'Auditoria creada con exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
