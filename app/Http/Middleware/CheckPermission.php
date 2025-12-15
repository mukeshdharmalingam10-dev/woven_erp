<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $form
     * @param  string  $type
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $form, string $type = 'read')
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        if (!auth()->user()->hasPermission($form, $type)) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
