<?php
namespace App\Http\Middleware;

use App\Exceptions\ApiExceptionHandler;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        die($request->input('token'));
        if ($request->input('token') !== 'my-secret-token') {
            // return redirect('/');
            throw new ApiExceptionHandler("the token is invalid", Response::HTTP_BAD_REQUEST);
        }
 
        return $next($request);
    }
}