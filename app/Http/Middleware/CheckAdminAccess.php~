<?php

namespace Alison\ProjectManagementAssistant\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'Доступ заборонено. Ви не маєте прав для доступу до адмін-панелі.');
        }

        return $next($request);
    }
}
