<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;

class ContactMessageController extends Controller
{
    /**
     * Store a message submitted through the contact form.
     */
    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        if (! $request->isFromBot()) {
            ContactMessage::create($request->validated());
        }

        return redirect()
            ->route('contact')
            ->with('status', __('Thanks for reaching out! We will get back to you soon.'));
    }
}
