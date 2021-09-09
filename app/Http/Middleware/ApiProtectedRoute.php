<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class ApiProtectedRoute extends BaseMiddleware
{
    use ApiResponser;

    private const HTTP_CODE = Response::HTTP_UNAUTHORIZED;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Throwable $e) {
            if ($e instanceof TokenInvalidException) {
                $message = "Token is Invalid";
                return $this->errorResponse($message, self::HTTP_CODE);
            } elseif ($e instanceof TokenExpiredException) {
                if ($request->route()->named('auth.refresh')) {
                    try {
                        $newToken = JWTAuth::refresh($request->bearerToken());
                        if (!empty($newToken)) {
                            return $this->respondWithToken($newToken);
                        }
                    } catch (JWTException $e) {
                        $message = 'Token is not refreshable';
                        return $this->errorResponse($message, self::HTTP_CODE);
                    }
                }
                $message = "Token is Expired";
                return $this->errorResponse($message, self::HTTP_CODE);
            } else {
                return $this->errorResponse($e->getMessage(), self::HTTP_CODE);
            }
        }

        return $next($request);
    }
}
