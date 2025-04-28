<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleCors
{
    public function handle(Request $request, Closure $next)
    {
        // السماح بالـ CORS لجميع المجالات
        $response = $next($request)
            ->header('Access-Control-Allow-Origin', '*') // يمكن تحديد المجال بدلاً من '*'
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization');

        // التعامل مع طلبات OPTIONS
        if ($request->getMethod() == 'OPTIONS') {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', '*') // يمكن تحديد المجال بدلاً من '*'
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization');
        }

        return $response;
    }

}
