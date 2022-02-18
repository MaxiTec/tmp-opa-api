<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        // remove admin
        return  User::with('roles')->where('status', '1')->whereHas('roles', function ($query) {
            $query->where('name', '<>','super-admin');
        })->paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // TODO: FormRequest
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            // 'email' => 'unique:users,email_address,'.$user->id //update
            // 'phone' => 'string||max:10',
            'phone' => 'required|regex:/[0-9]{10}/',
            'roles'=>'required'
        ]);
        //  dd($validator);
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        // return $request;
        if ($validator->fails()) {
            return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
        }
        $user = User::create($input);
        // return $user->id;
        $id = $user->id;
        // !We need to add permissions and roles
        $user->assignRole($request->input('roles'));
        // $success['name'] =  $user->name;
        $newUser = User::with('roles')->where('id', $id)->first();
        // return $newUser;
        return $this->sendResponse($newUser, 'User register successfully.');
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Nop
        return 'test';
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$id,
            // 'password' => 'required|min:6',
            'phone' => 'nullable|regex:/[0-9]{10}/',
            'roles'=>'required|array|min:1|'
        ]);
        //  dd($validator);
        // return $id;
        $input = $request->all();
        if ($validator->fails()) {
            return response(['error' =>$validator->messages()->first()],Response::HTTP_BAD_REQUEST);
        }
        // solamente pasamos los datos que queremos actualizar
        if(!empty($request->input('password'))){
            $input['password'] = bcrypt($input['password']);
        }
       $user =  User::find($id)->update($input);
       
    //    return $user->pluck('name');
       if($user){
        $user = User::with('roles')->where('id', $id)->first();
        // Actualizamos los roles
        if(!empty($request->input('roles'))){
            $user->syncRoles($request->input('roles'));
        }
        return $this->sendResponse($user, 'User updated successfully.');
       }else{
           return response(['error' =>'User not updated'],Response::HTTP_BAD_REQUEST);
       }
        
        // return $request->all();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Soft Delete (?) preguntar si deberria o no eliminarlos.
        try {
            $user = User::findOrFail($id);
            $user->status = !$user->status;
            $user->save();
            return response(['data' => $user]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }

    }
    public function disable($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_active = !$user->is_active;
            $user->save();
            return response(['data' => $user]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }

    }
}
