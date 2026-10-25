<?php

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pages render in english left-to-right by default', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<html lang="en" dir="ltr">', escape: false)
        ->assertSee('Ship ideas faster with Nimbus');
});

test('the language switcher links to every supported locale', function () {
    $this->get(route('home'))
        ->assertSee(route('locale.update', 'en'))
        ->assertSee(route('locale.update', 'ar'))
        ->assertSee('العربية');
});

test('switching the locale stores it in the session and redirects back', function () {
    $this->from(route('about'))
        ->post(route('locale.update', 'ar'))
        ->assertRedirect(route('about'))
        ->assertSessionHas('locale', 'ar');
});

test('switching the locale without a previous page redirects home', function () {
    $this->post(route('locale.update', 'ar'))
        ->assertRedirect(route('home'));
});

test('unsupported locales are not found', function () {
    $this->post('/locale/fr')->assertNotFound();
});

test('an unsupported locale in the session falls back to english', function () {
    $this->withSession(['locale' => 'fr'])
        ->get(route('home'))
        ->assertSee('<html lang="en" dir="ltr">', escape: false);
});

test('pages render in arabic right-to-left after switching', function (string $routeName, string $arabicText) {
    $this->withSession(['locale' => 'ar'])
        ->get(route($routeName))
        ->assertOk()
        ->assertSee('<html lang="ar" dir="rtl">', escape: false)
        ->assertSee($arabicText)
        ->assertSee('جميع الحقوق محفوظة');
})->with([
    'home' => ['home', 'حوّل أفكارك إلى واقع أسرع مع Nimbus'],
    'about' => ['about', 'تأسست عام 2024'],
    'contact' => ['contact', 'أرسل لنا رسالة'],
]);

test('every arabic translation has a value', function () {
    $arabicTranslations = json_decode(file_get_contents(lang_path('ar.json')), true, flags: JSON_THROW_ON_ERROR);

    expect($arabicTranslations)->each->toBeString()->not->toBeEmpty();
});

test('contact form validation messages are shown in arabic', function () {
    $this->withSession(['locale' => 'ar'])
        ->from(route('contact'))
        ->post(route('contact.store'), ['email' => 'not-an-email'])
        ->assertSessionHasErrors([
            'name' => 'حقل الاسم مطلوب.',
            'email' => 'يجب أن يكون حقل البريد الإلكتروني عنوان بريد إلكتروني صالحًا.',
        ]);
});

test('the contact confirmation is shown in arabic', function () {
    $this->withSession(['locale' => 'ar'])
        ->followingRedirects()
        ->post(route('contact.store'), ContactMessage::factory()->raw())
        ->assertSee('شكرًا لتواصلك معنا!');
});
