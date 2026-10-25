<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Arabic messages for the validation rules used by the application's forms.
    | Rules without a line here fall back to the English framework messages.
    |
    */

    'email' => 'يجب أن يكون حقل :attribute عنوان بريد إلكتروني صالحًا.',
    'max' => [
        'string' => 'يجب ألا يتجاوز حقل :attribute :max حرفًا.',
    ],
    'min' => [
        'string' => 'يجب أن يحتوي حقل :attribute على :min أحرف على الأقل.',
    ],
    'required' => 'حقل :attribute مطلوب.',
    'string' => 'يجب أن يكون حقل :attribute نصًا.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'subject' => 'الموضوع',
        'message' => 'الرسالة',
    ],

];
