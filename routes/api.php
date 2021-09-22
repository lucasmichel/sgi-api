<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

Route::group([
    'middleware' => 'apiJwt',
    'prefix' => 'auth'

], function ($router) {
    Route::get('/', function () {
        return view('welcome');
    });


    Route::group([
        'middleware' => ['user_super_admin']
    ], function () {

        Route::apiResource('/tenants', 'TenantController')->except(['store', 'update'])->names([
            'index'     => 'tenants.index',
            'show'      => 'tenants.show',
            'destroy'   => 'tenants.destroy'
        ]);
        Route::apiResource('/modules', 'ModuleController')->names([
            'index'     => 'modules.index',
            'store'     => 'modules.store',
            'show'      => 'modules.show',
            'update'    => 'modules.update',
            'destroy'   => 'modules.destroy'
        ]);
    });


    //Route::post('/login', [AuthController::class, 'login']);
    //Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    //Route::post('/roles', [RoleController::class, 'store']);
    Route::resource('roles', RoleController::class);
});
