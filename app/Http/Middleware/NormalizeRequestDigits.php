<?php

namespace App\Http\Middleware;

use App\Support\DigitNormalizer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeRequestDigits
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')) {
            $request->merge(DigitNormalizer::normalizeArray($request->query()));
        } elseif (in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            $request->merge(DigitNormalizer::normalizeArray($request->all()));
        }

        return $next($request);
    }
}
