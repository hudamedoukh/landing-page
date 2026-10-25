<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class NoBidiControlCharacters implements ValidationRule
{
    /**
     * Matches the invisible embedding, override, and isolate characters that can
     * visually reorder text. Plain direction marks (LRM, RLM, ALM) stay allowed
     * because they are legitimately used when mixing Arabic and English.
     */
    protected const string BIDI_CONTROL_CHARACTERS = '/[\x{202A}-\x{202E}\x{2066}-\x{2069}]/u';

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && preg_match(self::BIDI_CONTROL_CHARACTERS, $value)) {
            $fail('The :attribute field must not contain invisible text direction control characters.')->translate();
        }
    }
}
