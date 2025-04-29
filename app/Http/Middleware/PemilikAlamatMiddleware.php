<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PemilikAlamatMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $alamat = Alamat::findOrFail($request->id);
        if ($alamat->id_user != $user->user_id) {
            return response()->json('Alamat not found', 404);
        } else {
            return $next($request);
        }
    }
}
