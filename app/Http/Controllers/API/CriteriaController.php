<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Criteria;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $test = Criteria::all();
        return $test;
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
            'name' => 'required|string|max:100|unique:criteria,name',
        ]);
         
        if ($validator->fails()) {
            return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
        }
        $question = new Criteria($request->all());
        $question->save();

        return response([
            'data' => $question,
            // 'data' => new SectionResource($question),
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
