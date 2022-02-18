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
use Illuminate\Support\Facades\Auth;
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
        return new SectionCollection(Section::where('status',true)->get());
    }
    public function getAllFormattedResources(){
        $sections = Section::with(['areas','areas.criteria'])->where('status',true)->get();
        // Deberia usar solo un resource (?) mmm podria ser :v y si no tiene asiganda una seccion no se muestra? probar despues.
        $formatted = $sections->map(function($section){
            return [
                'title' => $section->name,
                'key' => 's-'.$section->id,
                'children' => $section->areas->map(function($area) use($section){
                    return [
                        'title' => $area->name,
                        'key' => 'a-'.$section->id.'-'.$area->id,
                        'children' => $area->criteria->map(function($criteria) use($section, $area){
                            return [
                                'title' => $criteria->name,
                                'key' => 'c-'.$section->id.'-'.$area->id.'-'.$criteria->id,
                            ];
                        })->toArray()
                    ];
                })->toArray()
             ];
        })->toArray();
        return $formatted;
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
            // 'description' => 'string|max:200', not necesary now
        ]);
         
        if ($validator->fails()) {
            return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
        }
        $section = new Section($request->all());
        $section->save();
        return response(new SectionResource($section), Response::HTTP_CREATED);
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
            },'areas.criteria'])
            // solo tiene area asignadas
            // ->whereHas('areas')
            // ->where('is_active', true)
            // ->where('status', true)
            ->findOrFail($id);
            return response(new SectionResource($section));
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
                // 'description' => 'string|max:200',
                // 'areas.*' => 'exists:areas,id',
            ]);
             
            if ($validator->fails()) {
                return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
            }
            $section = Section::with(['areas'=>function($query){
                // Solamente si las areas estan activas esto no debe tenerlo los admins
                    return $query->where('is_active',true)->where('status',true);
            }])
            // solo tiene area asignadas
            // ->whereHas('areas')
            ->where('is_active', true)
            ->where('status', true)
            ->findOrFail($id);

            $section->update($request->all());

            return response(new SectionResource($section),200);
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
        $user = Auth::user();
        // return $user;
        try {
            $section = Section::with(['areas'=>function($query) use($user){
                // Solamente si las areas estan activas esto no debe tenerlo los admins (preguntar)
                if($user->hasRole('administrator')){
                    return $query;
                }
                return $query->where('is_active',true)->where('status',true);
            }])
            // ->whereHas('areas')
            // ->where('is_active', true)
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

    public function toggleActive($id) {
        try {
            $section = Section::findOrFail($id);
            $section->is_active = !$section->is_active;
            $section->save();
            return response(['data' => new SectionResource($section),]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
        
    }
    // Crear nuevas areas y asignarlas a la seccion
    public function assignAreas(Request $request, $id){
        // return $id;
        $data = $request->all();
        // No se puede repetir el mismo nombre de Area por sección
        $validator = Validator::make($data, [
            'name' => ['required','string','max:100', Rule::unique('areas', 'name')->where('section_id', $id)],
        ]);
         
        if ($validator->fails()) {
            return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
        }
        try {
            $section = Section::where('is_active', true)
            ->where('status', true)
            ->findOrFail($id);
            if($section){
                $data['section_id']=$id;
                $section->areas()->create($data);
                // Preguntar como chingaos arreglar esto? Nunca podre ver los eliminados, esta bien?
                return response($section->load('areas'),200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
