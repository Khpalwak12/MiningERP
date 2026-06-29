<?php

namespace App\Http\Middleware;

use App\Http\Resources\FinancialYearResource;
use App\Services\FinancialYearService;
use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'locale' => $user->locale,
                    'roles' => $user->getRoleNames()->values()->all(),
                    'permissions' => $user->getAllPermissions()->pluck('name')->values()->all(),
                ] : null,
            ],
            'locale' => app()->getLocale(),
            'translations' => [
                'en' => trans('erp', [], 'en'),
                'ps' => trans('erp', [], 'ps'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'activeFinancialYear' => fn () => FinancialYearResource::optional(
                app(FinancialYearService::class)->activeYear()
            ),
            'isAllYearsMode' => fn () => app(FinancialYearService::class)->isAllYearsMode(),
            'todayShamsi' => fn () => JalaliDate::today(),
        ];
    }
}
