<?php
/*jwt foi com este https://www.positronx.io/laravel-jwt-authentication-tutorial-user-login-signup-api/#tc_10336_02
// foi adicionado os arquivos:
/opt/lampp/htdocs/sgi-api/app/Http/Middleware/ApiProtectedRoute.php
/opt/lampp/htdocs/sgi-api/app/Traits/ApiResponser.php
foi alterado o :
/opt/lampp/htdocs/sgi-api/app/Http/Kernel.php
foi adicionado o ApiProtectedRoute:
em protected $routeMiddleware = [

    'apiJwt' => \App\Http\Middleware\ApiProtectedRoute::class,


fazer isso agora

https://www.itsolutionstuff.com/post/laravel-8-user-roles-and-permissions-tutorialexample.html
https://github.com/spatie/laravel-permission
https://github.com/Trinity-Solucoes/Fastmanager-3-backend/blob/develop/Modules/Common/Routes/api_v1.php

*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        //define as rotas com permissao de executar sem estar logado
        //$this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->middleware('apiJwt', ['except' => ['login', 'register', 'user-profile']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

}
