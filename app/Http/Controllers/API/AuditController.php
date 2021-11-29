<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Audit;


use App\Models\Property;
use App\Models\Section;
use App\Models\Question;

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
        $data = Audit::with(['user','program'=>function($query) {
            return $query->leftJoin('properties as p','p.id','=','programs.property_id')
                        ->select('programs.*','p.name as property_name');
        },'questions'=>function($query){
            return $query->leftJoin('area_criteria as ac','ac.id','=','questions.area_criteria_id')
            ->leftJoin('areas as a','a.id','=','ac.area_id')
            ->leftJoin('criteria as c','c.id','=','ac.criteria_id')
            ->leftJoin('sections as s','s.id','=','a.section_id')
            ->select('questions.*','c.name as criteria_name','a.name as area','s.name as section');
        }])->first();

        // $grouped = $data->questions()->groupBy(['section','area']);
        // $grouped = $data->groupBy('program.property_name');
        return $data->questions;
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
        $property = Property::findOrFail($id);

        // Saco todos los ids de Criteria Area (preguntas del hotel)

        // Todas las preguntas que tiene asociado 
        // $criteria = $property->CriteriaByArea->pluck('id');

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
        foreach ($grouped as $key => $section) {
            foreach ($section as $key2 => $area) {
                $questions = [];
                foreach ($area as $key3 => $question) {
                    $question_id = Question::create(['area_criteria_id'=>$question->criteria_id]);
                    // $questions[] = ['area_criteria_id'=>$question->criteria_id];
                    $data = [
                        'programs_id'=>$question->programs_id,
                        'user_id' => $user_id,
                        'admin_id' => $userAdmin->id,
                        'executed_date' => Carbon::now()
                    ];
                }
                // por cada area creo las preguntas y despues se las asigno a la auditoria
               $questions[] = $question_id->id;
               $auditoria =  Audit::create($data);
               $auditoria->questions()->attach($questions);
            }
            
        }
        return $questions;
        // return sendResponse($auditoria, 'Auditoria creada con exito');
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
