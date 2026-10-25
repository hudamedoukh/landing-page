<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Remember the visitor's chosen locale and send them back where they were.
     */
    public function update(Request $request, string $locale): RedirectResponse
    {
        $request->session()->put('locale', $locale);

        return redirect()->back(fallback: route('home'));
    }
}
