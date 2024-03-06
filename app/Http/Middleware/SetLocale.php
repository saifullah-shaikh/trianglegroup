<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('lang')) {
            $locale = session()->get('locale') ?? config('app.locale');
            App::setLocale($locale);
        }

        return $next($request);
    }
}
