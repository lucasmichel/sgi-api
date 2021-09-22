<?php

namespace Modules\System\Http\Middleware;

use Closure;
use Modules\System\Exceptions\UnauthorizedException;

class UserSuperAdminMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (app('auth')->guard($guard)->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        if (! app('auth')->guard($guard)->user()->isSuperAdmin()) {
            throw UnauthorizedException::forSuperadmin();
        }

        return $next($request);
    }
}
