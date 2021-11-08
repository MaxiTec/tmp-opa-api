<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Area;
use App\Http\Resources\SectionCollection;
use App\Http\Resources\SectionResource;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO: move to resource
        return new SectionCollection(Section::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Is not necessary a Form Request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:sections,name',
            'description' => 'string|max:200',
            // 'areas' => 'array', //not required
            // 'areas.*' => 'exists:areas,id',
        ]);
         
        if ($validator->fails()) {
            return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
        }
        $section = new Section($request->all());
        $section->save();
        return response([
            'data' => new SectionResource($section),
        ], Response::HTTP_CREATED);
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
            $section = Section::with(['areas'=>function($query){
                // Solamente si las areas estan activas esto no debe tenerlo los admins
                    $query->where('is_active',true)->where('status',true);
            }])
            // solo tiene area asignadas
            // ->whereHas('areas')
            // ->where('is_active', true)
            // ->where('status', true)
            ->findOrFail($id);
            return response(['data' => new SectionResource($section)]);
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
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:sections,name,'.$id,
                'description' => 'string|max:200',
                // 'areas' => 'array', //not required
                // 'areas.*' => 'exists:areas,id',
            ]);
             
            if ($validator->fails()) {
                return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
            }
            $section = Section::with(['areas'=>function($query){
                // Solamente si las areas estan activas esto no debe tenerlo los admins
                    $query->where('is_active',true)->where('status',true);
            }])
            // solo tiene area asignadas
            // ->whereHas('areas')
            ->where('is_active', true)
            ->where('status', true)
            ->findOrFail($id);

            $section->update($request->all());

            return response(['data' => new SectionResource($section)]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }

        // $links = array(
        //     new Link(),
        //     new Link()
        // );
        // we delete all areas and then save news
        // $section->areas()->delete();
        // $section->areas()->saveMany($links);

        // unset($validated['massage']['type']);
        // $massage = Massage::find($service->massage->id);
        
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
            $section = Section::with(['areas'=>function($query){
                // Solamente si las areas estan activas esto no debe tenerlo los admins
                    $query->where('is_active',true)->where('status',true);
            }])
            // solo tiene area asignadas
            // ->whereHas('areas')
            ->where('is_active', true)
            ->where('status', true)
            ->findOrFail($id);
            
            if($section){
                $section->status = 0;
                $section->save();
                return response([
                    'data' => new SectionResource($section),
                ]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
        
    }

    // Crear nuevas areas y asignarlas a la seccion
    public function assignAreas(Request $request, $id){
        // return $id;
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => ['required','string','max:100', Rule::unique('areas', 'name')->where('section_id', $id)],
        ]);
         
        if ($validator->fails()) {
            return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
        }
        try {
            $section = Section::with('areas')
            // ->whereHas('areas')
            ->where('is_active', true)
            ->where('status', true)
            ->findOrFail($id);
            if($section){
                $data['section_id']=$id;
                $section->areas()->create($data);
                return response([
                    'data' => new SectionResource($section->load('areas')),
                ]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
