<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Audit;


use App\Models\Property;
use App\Models\Section;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        // Data of hotel
        $property = Property::find($id);

        // Saco todos los ids de Criteria Area (preguntas del hotel)
        // $criteria = $property->CriteriaByArea⁄;

        // Todas las preguntas que tiene asociado 
        $criteria = $property->CriteriaByArea->pluck('id');
        // $criteria = $property->CriteriaByArea->pluck('criteria_id');
        // y hago una comparacion de todas las preguntas por secciones y areas y "checkeamos" los que tengan el hotel
        // return $criteria;
        $questions = [];
        foreach ($catalog as $key1 => $section) {
            foreach ($section->areas as $key2 => $area) {
                // Aca guardaremos todos las preguntas que tendra cada sección

                foreach ($area->criteria as $key3 => $question) {
                    if(in_array($question->pivot->id, $criteria->toArray())){
                        $questions[$key1]['question_id']=$question->pivot->criteria_id;
                    }
                    // array_push($question,array('question_id'=>$question->pivot->id));
                }
                
                // Model::insert($data); // Eloquent approach
                // Audit::
                // Creamos una nueva Auditoria por seccion  ya que cada auditoria tiene observaciones
              

            }
        }
        return $questions;
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
