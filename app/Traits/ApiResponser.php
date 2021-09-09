<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

trait ApiResponser
{
    /**
     * Build succes response
     * @param string|array $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($message, $data = [], int $code = Response::HTTP_OK)
    {

        $json = [
            'success' => true,
            'message' => $message,
            'data'  => null
        ];

        if (!empty($message)) {
            $json = array_merge($json, $data);
        } else {
            $json['data'] = $data;
        }

        return response()->json($json, $code);
    }

    /**
     * Build error response
     * @param string|array $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($message, int $code = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'status_code' => $code
        ], $code);
    }

    /**
     * Build token response
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken(string $token, $allPermissions = null, $user = null, $sync = null)
    {
        $json = [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL()
        ];

        $json = collect($json);

        if (!empty($user)) {
            $json = $json->merge(['user' => $user]);
        }

        if (!empty($sync)) {
            $json = $json->merge($sync);
        }

        if (!empty($allPermissions)) {
            $json = $json->merge($allPermissions);
        }

        return response()->json($json, Response::HTTP_OK, ['Authorization' => $token]);
    }
}
