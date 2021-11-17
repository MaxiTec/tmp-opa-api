<?php

namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Validator;
use Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

// Extendemos de BaseController para usar los metodos de Response
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('role:administrador', ['only' => ['register']]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'last_name' => 'required',
            'phone' => 'nullable|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'roles'=>'required' // or roles?
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        // !We need to add permissions and roles
        $user->assignRole($request->input('roles'));
        // Tomamos nombre de App;
        $success['token'] =  $user->createToken(config('app.name'))->accessToken;
        $success['name'] =  $user->name;
        $success['last_name'] =  $user->last_name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // we Login with this credentials
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            // $user = App\User::findByEmail($request->input('email'))->firstOrFail();
            // revisamos roles asiganos
            $roles = $user->roles;
            // usuario no se le asigno roles
            if(!boolval($roles->count())) {
                return response()->json(['error' => 'Usuario sin rol'], 401);
            }
            // revisamos permisos por ROL
            foreach($roles as $role) {
                $role->permissions;
            }
            $permissions = $roles->map(function($item, $key) {
                return $item->permissions->map(function($item, $key){
                    return Str::slug($item->name);
                });
            })->flatten()->values()->all();

            $success['token'] =  $user->createToken(config('app.name'))->accessToken; 
            $success['name'] =  $user->name;
            $success['last_name'] =  $user->last_name;
            $success['roles'] =  $roles;
            $success['permissions'] =  $permissions;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorized.', ['error'=>'Unauthorized']);
        } 
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->sendResponse(null, 'Successfully logged out');
    }

    // // TODO: Role Controller
    // public function roles(Request $request)
    // {
    //     $roles = Role::orderBy('id','DESC')->paginate(5);
    //     return $this->sendResponse($success, 'Successfully logged out');
    // }
}