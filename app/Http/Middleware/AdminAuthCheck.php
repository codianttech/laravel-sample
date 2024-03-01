<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthCheck
{

    /**
     * Method handle
     *
     * @param Request $request [explicite description]
     * @param Closure $next    [explicite description]
     * @param string  $guard   [explicite description]
     *
     * @return void
     */
    public function handle(Request $request, Closure $next, string $guard)
    {
        if (Auth::guard($guard)->Check() && User::TYPE_ADMIN == Auth::guard($guard)->user()->user_type) {
            $status = [User::STATUS_INACTIVE];
            if (in_array(Auth::guard($guard)->user()->status, $status)) {
                return redirect()->route('admin.logout');
            }

            return $next($request);
        }
        $type = $request->headers->get('Content-Type');
        if ('application/json' == $type || $request->ajax()) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => '',
                ],
                401
            );
        }

        return redirect()->route('admin.login');
    }
}
