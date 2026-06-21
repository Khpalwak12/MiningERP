<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', Rule::in(['en', 'ps'])],
        ]);

        if ($request->user()) {
            $request->user()->update(['locale' => $validated['locale']]);
        }

        session(['locale' => $validated['locale']]);
        app()->setLocale($validated['locale']);

        return back();
    }
}
