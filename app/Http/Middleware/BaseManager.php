<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * Class Admin
 * @package App\Http\Middleware
 */
class BaseManager
{
    use AuthenticatesUsers;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \RuntimeException
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->role < 4){
            if ($request->user()->suspended == 1) {
                $this->guard()->logout();

                $request->session()->flush();

                $request->session()->regenerate();

                return redirect()
                    ->route('login')
                    ->with('warning', 'Votre compte a été bloqué.');
            }
            return $next($request);
        }

        return redirect()->back();
    }
}
