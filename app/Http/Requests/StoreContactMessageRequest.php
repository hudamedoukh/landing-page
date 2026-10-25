<?php

namespace App\Http\Requests;

use App\Rules\NoBidiControlCharacters;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    /**
     * The hidden field that only automated spam bots fill in.
     */
    public const string HONEYPOT_FIELD = 'website';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new NoBidiControlCharacters],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255', new NoBidiControlCharacters],
            'message' => ['required', 'string', 'min:10', 'max:5000', new NoBidiControlCharacters],
        ];
    }

    /**
     * Determine if the submission filled in the honeypot field, which humans never see.
     */
    public function isFromBot(): bool
    {
        return $this->filled(self::HONEYPOT_FIELD);
    }
}
