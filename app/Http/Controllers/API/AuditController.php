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
    public function index(Request $request)
    {
        $data = $request->all();
        // dd($data);
        $audits = Audit::with(['user'=>function($query){
            return $query->select('id','name','last_name');
        },'program'=>function($query) {
            return $query->leftJoin('properties as p','p.id','=','programs.property_id')
                        ->leftJoin('area_criteria as ac','ac.id','=','programs.area_criteria_id')
                        ->leftJoin('areas as a','a.id','=','ac.area_id')
                        ->leftJoin('sections as s','s.id','=','a.section_id')
                        ->select('programs.*','p.name as property_name','a.name as area','s.name as section');
        },'questions'=>function($query){
            return $query->leftJoin('area_criteria as ac','ac.id','=','questions.area_criteria_id');
            // ->select('questions.*','c.name as criteria_name','a.name as area','s.name as section','s.id as section_id');
        }])
        ->whereHas('program',function($query) use ($request){
            return $query->leftJoin('properties as p','p.id','=','programs.property_id')
                         ->where('property_id',$request->input('property'))
                         ->where('p.is_active', true);
        })
        ->whereHas('questions',function($query) use ($request){
            return $query->leftJoin('area_criteria as ac','ac.id','=','questions.area_criteria_id')
            ->leftJoin('areas as a','a.id','=','ac.area_id')
            ->leftJoin('sections as s','s.id','=','a.section_id')
            ->where('s.id',$request->input('section'));
        })
        ->get();

        // dd($audits);
        // una vez que tenemos todas las auditorias agregamos funciones para las collections
        foreach ($audits as $key => $audit) {
            $percentage = $this->getPercentageAudited($audit);
            $audit->percentage = $percentage;
            $audit->totalQ = $audit->questions->count();
        }
       
        // Percentage per Status
        $getPercentage = $this->getPercentagePerStatus($audits);
        $groupedBySections = $audits->groupBy('program.section');

        foreach ($groupedBySections as $key => $section) {
            // return $section->avg('percentage');
            // $percentage = $this->getPercentageBySection($section);
            // number_format((float)$section->avg('percentage'), 2, '.', '')
            $groupedBySections[$key] = number_format((float)$section->avg('percentage'), 2, '.', '');
        }

        $success['audited_areas'] =  $getPercentage;
        $success['audited_by_sections'] =  $groupedBySections;

        $critical_areas = array_merge([], $audits->where('percentage','<',60)->groupBy('program.area')->all());
        $success['critical_areas'] =  $critical_areas; 
        // return  $getPercentage;
        return $this->sendResponse($success, '');
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
        $audit = Audit::with(['user'=>function($query){
            return $query->select('id','name','last_name');
        },'program'=>function($query) {
            return $query->leftJoin('properties as p','p.id','=','programs.property_id')
                        ->leftJoin('area_criteria as ac','ac.id','=','programs.area_criteria_id')
                        ->leftJoin('areas as a','a.id','=','ac.area_id')
                        ->leftJoin('sections as s','s.id','=','a.section_id')
                        ->select('programs.*','p.name as property_name','a.name as area','s.name as section');
        },'questions'=>function($query){
            return $query->leftJoin('area_criteria as ac','ac.id','=','questions.area_criteria_id')
                         ->leftJoin('criteria as c','c.id','=','ac.criteria_id')
                         ->select('c.*','questions.*');
        }])
        ->findOrFail($id);
        return $this->sendResponse($audit, 'yeah');
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

    private function getPercentageAudited($audit)
    {   
        // dd($questions);
        // $collect = collect($questions);
        $total = $audit->questions()->wherePivot('not_apply', 0)->count();

        $checked = $audit->questions()->wherePivot('check',true)->count();
        // dd($total, $checked);
        $percentage = ($checked/$total) * 100;
        // return $percentage;
        // dd($percentage);
        return number_format((float)$percentage, 2, '.', '');
    }
    private function getPercentagePerStatus($audits)
    {
        // dd($audits->toArray());
        $res = [];
        $total = $audits->count();
        $grouped = $audits->countBy(function ($item, $key) {
            if($item->percentage  >= 80){
                return 'Acceptable';
             }elseif($item->percentage < 80 && $item->percentage  >= 60){
                return 'Below Standard';
             }else{
                return 'Critical';
             }
        });
        // dd($grouped);
        foreach ($grouped as $key => $value) {
            $res[] = [
                'status'=>$key,
                'percentage'=>number_format((float)($value/$total)*100, 2, '.', ''),
                'count'=> $value
            ];
        }
        return ['percentage' => $res, 'total' => $total];
    }

    public function checkAudit(Request $request, $id)
    {
        $audit = Audit::findOrFail($id);
        $questions = $audit->questions;
        $req = $request->all();
        // dd($questions);
        foreach ($req['questions'] as $key => $value) {
            // Preguntar por esto
            $test = $audit->questions()->select('audits_questions.id');
            // ->where('audit_questions.id', $value['id']);
            // dd($test->get());
            // $test->update(['check'=> $value['check'], 'not_apply'=>$value['not_apply']]);
            $audit->questions()->updateExistingPivot($value['id'], ['check'=> $value['check'], 'not_apply'=>$value['not_apply']]);
        }
        return $this->sendResponse($audit->fresh('questions'),  'yeah');
    }

 }