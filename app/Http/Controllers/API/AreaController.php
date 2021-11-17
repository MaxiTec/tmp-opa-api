<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
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
        $res = Area::with('section')
        ->orderBy('name', 'desc')
        ->get();
        return AreaResource::collection($res)->groupBy('section.name');
    
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
    public function update(Request $request, $id)
    {
        $data = $request->all();
        try {
            $validator = Validator::make($data, [
                'name' => ['required','string','max:100', Rule::unique('areas', 'name')->ignore($id)->where('section_id', $id)],
            ]);
             
            if ($validator->fails()) {
                return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
            }

            $area = Area::findOrFail($id);

            $area->update($data);

            return response(['data' => new AreaResource($area)]);
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
        try {
            $area = Area::with(['section'=>function($query){
                // Solamente si las areas estan activas esto no debe tenerlo los admins
                    $query->where('is_active',true)->where('status',true);
            }])
            ->where('is_active', true)
            ->where('status', true)
            ->findOrFail($id);
            if($area){
                // cambiamos status a 0
                $area->status = 0;
                $area->save();
                return response([
                    'data' => new AreaResource($area),
                ]);
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

    //create a new area and assign it to a section
}
