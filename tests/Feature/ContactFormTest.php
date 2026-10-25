<?php

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('contact page shows the contact form', function () {
    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('Send us a message')
        ->assertSee(route('contact.store'));
});

test('a valid message is saved and the visitor is redirected with a confirmation', function () {
    $attributes = ContactMessage::factory()->raw();

    $this->post(route('contact.store'), $attributes)
        ->assertRedirect(route('contact'))
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status');

    $this->assertDatabaseHas('contact_messages', $attributes);
});

test('the subject is optional', function () {
    $attributes = ContactMessage::factory()->raw(['subject' => null]);

    $this->post(route('contact.store'), $attributes)
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('contact_messages', 1);
});

test('the confirmation message is shown after submitting', function () {
    $this->followingRedirects()
        ->post(route('contact.store'), ContactMessage::factory()->raw())
        ->assertOk()
        ->assertSee('Thanks for reaching out!');
});

test('invalid input is rejected', function (array $overrides, string $invalidField) {
    $attributes = array_merge(ContactMessage::factory()->raw(), $overrides);

    $this->from(route('contact'))
        ->post(route('contact.store'), $attributes)
        ->assertRedirect(route('contact'))
        ->assertSessionHasErrors($invalidField);

    $this->assertDatabaseCount('contact_messages', 0);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'name too long' => [['name' => str_repeat('a', 256)], 'name'],
    'missing email' => [['email' => ''], 'email'],
    'invalid email' => [['email' => 'not-an-email'], 'email'],
    'subject too long' => [['subject' => str_repeat('a', 256)], 'subject'],
    'missing message' => [['message' => ''], 'message'],
    'message too short' => [['message' => 'Hi'], 'message'],
    'message too long' => [['message' => str_repeat('a', 5001)], 'message'],
]);

test('submissions are rate limited', function () {
    foreach (range(1, 5) as $attempt) {
        $this->post(route('contact.store'), ContactMessage::factory()->raw())
            ->assertRedirect();
    }

    $this->post(route('contact.store'), ContactMessage::factory()->raw())
        ->assertTooManyRequests();

    $this->assertDatabaseCount('contact_messages', 5);
});
