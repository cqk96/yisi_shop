<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        if (! Auth::user()->is_super_admin) {
            Auth::logout();

            return redirect()->route('admin.login')->withErrors('当前账号没有后台权限。');
        }

        return $next($request);
    }
}
