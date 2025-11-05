<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckPermission
{
    /**
     * Menangani permintaan dan memastikan user punya permission.
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = $request->user();

        if (! $user || ! $user->hasPermission($permission)) {
            throw new AccessDeniedHttpException('Anda tidak memiliki hak akses: ' . $permission);
        }

        return $next($request);
    }
}

