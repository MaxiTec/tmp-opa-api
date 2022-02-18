<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Criteria;
use App\Http\Resources\AreaResource;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $res = Area::withCount('section')->where('status',true)
        ->orderBy('name', 'desc')->paginate(10);
        // ->get();
        // return $res;
        return AreaResource::collection($res);
        // return AreaResource::collection($res)->groupBy('section.name');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // * We Store in Section Controller
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $area = Area::with(['section'=>function($query){
                    $query->where('is_active',true)->where('status',true);
            }])
            // solo tiene area asignadas
            // ->whereHas('areas')
            // ->where('is_active', true)
            // ->where('status', true)
            ->findOrFail($id);
            return response(['data' => new AreaResource($area)]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $area_id)
    {
        $data = $request->all();
        try {
            $validator = Validator::make($data, [
                'name' => ['required','string','max:100', Rule::unique('areas', 'name')->ignore($area_id)->where('section_id', $request->input('section_id'))],
            ]);
             
            if ($validator->fails()) {
                return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
            }

            $area = Area::findOrFail($area_id);
            $area->update($data);
            return response(new AreaResource($area),200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Preguntar si debo eliminar o no Las areas ya que estan eliminarian sus Items ( servira de algo conserrvarlas?)
        // si no las elimino no se eliminan sus ITEMS
        try {
            $area = Area::with(['section'])
            // ->where('is_active', true)
            ->where('status', true)
            ->findOrFail($id);
            if($area){
                $area->status = 0;
                // Debo Eliminar (?) ya que aparecen en la tabla area_criteria....
                $area->save();
                return response(new AreaResource($area),200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function toggleStatus($id) {
        try {
            $area = Area::findOrFail($id);
            $area->status = !$area->status;
            $area->save();
            return response(['data' => new AreaResource($area),]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
        
    }
    public function toggleActive($id) {
        try {
            $area = Area::findOrFail($id);
            $area->is_active = !$area->is_active;
            $area->save();
            return response(['data' => new AreaResource($area),]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
        
    }

    public function assignCriteria(Request $request, $id){
        // we get area id and creiteria id's
        $data = $request->all();
        // return $data;
        try {
            $validator = Validator::make($data, [
                'criteria' => ['required','array'],
                'criteria.*' => 'exists:criteria,id',
            ]);
             
            if ($validator->fails()) {
                return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
            }

            $area = Area::findOrFail($id);
            // return $data['criteria'];
            $area->criteria()->sync($data['criteria']);

            return response(['data' => new AreaResource($area)]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function addQuestion(Request $request, $id){
        $question = Criteria::firstOrCreate(['name' => $request->input('name')]);
        try {
            $area = Area::findOrFail($id);
            $criterias = $area->criteria->pluck('id')->toArray();
            // return $criterias;
            if(in_array($question->id, $criterias)){
                return response(['error' => 'Criteria already exists'],Response::HTTP_BAD_REQUEST);
            }
            $area->criteria()->attach($question->id);
            return response($area->load('criteria') ,200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
    }
    public function removeQuestion(Request $request, $id){//area_id
        try {
            $area = Area::findOrFail($id);
            $criterias = $area->criteria->pluck('id')->toArray();
            $filteredCriterias = collect($criterias)->filter(function($item) use($request){
                return $item != $request->input('criteria_id');
            })->all();
            $area->criteria()->sync($filteredCriterias);
            return response($area->load('criteria'),200);
            // return response( new AreaResource($area->load('criteria')),200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
    }

    //create a new area and assign it to a section
}
