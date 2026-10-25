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

        $previousUrl = url()->previous();

        if (parse_url($previousUrl, PHP_URL_HOST) !== $request->getHost()) {
            return redirect()->route('home');
        }

        return redirect()->to($previousUrl);
    }
}
