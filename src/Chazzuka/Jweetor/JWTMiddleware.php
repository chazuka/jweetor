<?php namespace Chazzuka\Jweetor;

use Closure;
use Illuminate\Contracts\Routing\Middleware as MiddlewareContract;

class JWTMiddleware implements MiddlewareContract {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     * @throws
     */
    public function handle($request, Closure $next)
    {
        try
        {
            app('jwt')->authenticateFromRequest($request);
        }
        catch (JWTException $e)
        {
            if ($request->wantsJson() || $request->ajax()) {
                return response($e->getMessage(), $e->getStatusCode(), $e->getHeaders());
            }

            throw $e;
        }

        return $next($request);
    }
}
