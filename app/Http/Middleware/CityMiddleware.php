<?php

namespace App\Http\Middleware;

use App\Models\City;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $city = ltrim(\request()->route()->getPrefix(), '/');
        $uri = $request->path();
//        dump($city, $uri);
        if (!$city && session('city')) {
            return redirect('/' . session('city.slug') . "/$uri", 301);
        }

        if (!$city && Route::currentRouteName() != 'index') {
            abort(404);
        }

        if ($city) {
            $city_data = City::query()->where('slug', '=', $city)->firstOrFail();
            session(['city' => $city_data]);
        }

        return $next($request);
    }
}
