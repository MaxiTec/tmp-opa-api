<?php

namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Config;
use Illuminate\Support\Str;

// Extendemos de BaseController para usar los metodos de Response
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            // 'rol'=>'required',
            // 'permissions'=>'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        if(!$request->has('roles')){
            // $rol = buscar rol
            $user->assignRole($rol);
        }else{
            // Default Role
            $user->assignRole($role1);
        }
        $input['password'] = bcrypt($input['password']);
        // Tomamos datos de User, Agregamos nuevos campos desde las imagraciones
        $user = User::create($input);
        // !We need to add permissions and roles
        // Tomamos nombre de App;
        $success['token'] =  $user->createToken(config('app.name'))->accessToken;
        $success['name'] =  $user->name;
   
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
            // juntamos todos los roles para mandarlos al FRONT
            $permissions = $roles->map(function($item, $key) {
                return $item->permissions->map(function($item, $key){
                    return Str::slug($item->name);
                });
            })->flatten()->values()->all();

            $success['token'] =  $user->createToken(config('app.name'))->accessToken; 
            $success['name'] =  $user->name;
            $success['roles'] =  $roles;
            $success['permissions'] =  $permissions;
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorized.', ['error'=>'Unauthorized']);
        } 
    }
}