<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Section;
use App\Http\Resources\PropertyResource;
// use App\Http\Controllers\DOSpacesController as DoSpace;
use App\Http\Requests\PropertyPostRequest;
use App\Http\Traits\UploadImageTrait;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\SectionCollection;
use App\Http\Resources\SectionResource;
use DB;

// class RegisterController extends BaseController
class PropertyController extends BaseController
{
    public function __construct()
    {
        $this->middleware('role:administrador');
        $this->middleware('role:administrador|auditor')->only('index', 'show');
    }

    use UploadImageTrait;

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PropertyResource::collection(Property::where('is_active',true)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PropertyPostRequest $request)
    {
        $file = $request->file('brand_img');
        $datos = $request->all();
        // return $file;
        $is_upload = $this->uploadImage($request,'hotels');

        if($is_upload){
            $datos['brand_img'] = $is_upload;
        }
        $property = Property::create($datos);
            return response([
                'data' => new PropertyResource($property),
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
            $area = Property::findOrFail($id);
            return response(['data' => new PropertyResource($area)]);
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
        $datos = $request->all();
        $validator = Validator::make($datos, [
            'name'=> 'string',
            'manager' => 'string|max:100',
            'code' => 'unique:properties,code,'.$id,
            'brand_img' => 'image|max:1024',
            'address' => 'string',
            'phone' => 'string',
            'lat' => 'string',
            'lon' => 'string',
            'phone_code' => 'string',
            'rooms' => 'string'
        ]);
         
        if ($validator->fails()) {
            return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
        }

        
        try {
            $property = Property::findOrFail($id);
            if($request->has('brand_img')){
                // Si quiere cambiar la imagen
                if(!empty($property->brand_img)){
                    // Eliminamos la imagen anterior
                    // return $property->brand_img
                    $this->deleteImage($property->brand_img,'hotels');
                    // return 'SI tiene IMAGEn';
                }
                // return 'NO TIENE IMAGENES';
                $is_upload = $this->uploadImage($request,'hotels');
                $datos['brand_img'] = $is_upload;
            }
            $property->update($datos);

            return response([
                'data' => new PropertyResource($property),
            ], Response::HTTP_CREATED);
            return response(['data' => new PropertyResource($area)]);
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
            $property = Property::findOrFail($id);
            $property->is_active = 0;
            $property->save();
            return response([
                'data' => new PropertyResource($property),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }
    }
    // Esto puede estar en sections
    public function showCriteria($id){
        // obtenemos id del hotel para ir asignando del template
        $catalog = Section::with('areas','areas.criteria')->get();
        // return $catalog;
        return new SectionCollection($catalog);
    }
    public function AssignToProperties(Request $request , $id){
        $property = Property::findOrFail($id);
        $property->CriteriaByArea()->sync($request->input('questions'));
    }
    public function ProgramByHotel(Request $request , $id){
        // get all questions in this Hotel
        $catalog = Section::with('areas','areas.criteria')->get();
        // Data of hotel
        $property = Property::findOrFail($id);

        // Saco todos los ids de Criteria Area (preguntas del hotel)
        $criteria = $property->CriteriaByArea->pluck('id');

        // $criteria = DB::table('programs')->where('property_id',$id)->get()->pluck('area_criteria_id');

        // y hago una comparacion de todas las preguntas por secciones y areas y "checkeamos" los que tengan el hotel
        foreach ($catalog as $key => $section) {
            foreach ($section->areas as $key => $area) {
                foreach ($area->criteria as $key => $question) {
                    $question->checked = in_array($question->pivot->id, $criteria->toArray());
                }   
            }
        }
        $success['property'] =  new PropertyResource($property);
        $success['sections'] =  new SectionCollection($catalog);
        return $this->sendResponse($success, '');
    }

    public function duplicate(PropertyPostRequest $request, $id)
    {
        $file = $request->file('brand_img');
        $datos = $request->all();

        $is_upload = $this->uploadImage($request,'hotels');
        $tmplHotel = Property::find($id);

        if($is_upload){
            $datos['brand_img'] = $is_upload;
        }
        // Acá hacemos la redireccion de los datos
        $property = Property::create($datos);
        $questions = $tmplHotel->CriteriaByArea->pluck('id');
        // return $questions;
        $property->CriteriaByArea()->sync($questions);

        return $this->sendResponse($property, 'Hotel duplicated successfully');
    }
}
