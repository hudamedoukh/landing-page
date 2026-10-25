<?php

use App\Models\ContactMessage;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;

uses(RefreshDatabase::class);

/**
 * Re-run the application's boot logic as if the app had started in the given environment.
 */
function bootApplicationAs(string $environment): void
{
    app()->detectEnvironment(fn () => $environment);
    app()->getProvider(AppServiceProvider::class)->boot();
}

/**
 * Extract the nonce from a response's Content-Security-Policy header.
 */
function cspNonce(string $contentSecurityPolicy): string
{
    preg_match("/'nonce-([^']+)'/", $contentSecurityPolicy, $matches);

    return $matches[1];
}

describe('1. security headers', function () {
    test('web responses send the browser security headers', function () {
        $this->get(route('home'))
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
            ->assertHeaderMissing('X-Powered-By');
    });

    test('the content security policy blocks framing, plugins, and foreign form targets', function () {
        $contentSecurityPolicy = $this->get(route('home'))->headers->get('Content-Security-Policy');

        expect($contentSecurityPolicy)
            ->toContain("default-src 'self'")
            ->toContain("frame-ancestors 'none'")
            ->toContain("object-src 'none'")
            ->toContain("base-uri 'self'")
            ->toContain("form-action 'self'");
    });

    test('scripts only run when they carry the per-request nonce', function () {
        $response = $this->get(route('home'));
        $contentSecurityPolicy = $response->headers->get('Content-Security-Policy');
        $nonce = cspNonce($contentSecurityPolicy);

        preg_match('/script-src ([^;]+)/', $contentSecurityPolicy, $scriptSources);

        expect($scriptSources[1])->not->toContain("'unsafe-inline'");
        $response->assertSee('<script type="module" src="', escape: false)
            ->assertSee('nonce="'.$nonce.'"', escape: false);
    });

    test('every response gets a fresh nonce', function () {
        $firstNonce = cspNonce($this->get(route('home'))->headers->get('Content-Security-Policy'));
        $secondNonce = cspNonce($this->get(route('home'))->headers->get('Content-Security-Policy'));

        expect($firstNonce)->not->toBe($secondNonce);
    });

    test('the vite dev server is allowed while it is running', function () {
        $hotFile = tempnam(sys_get_temp_dir(), 'hot');
        file_put_contents($hotFile, 'http://127.0.0.1:5173');
        Vite::useHotFile($hotFile);

        $contentSecurityPolicy = $this->get(route('home'))->headers->get('Content-Security-Policy');

        expect($contentSecurityPolicy)
            ->toContain("script-src 'self' 'nonce-")
            ->toContain('http://127.0.0.1:5173')
            ->toContain('ws://127.0.0.1:5173');

        unlink($hotFile);
    });

    test('strict transport security is only sent over https', function () {
        $this->get('https://localhost/')
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        $this->get('http://localhost/')
            ->assertHeaderMissing('Strict-Transport-Security');
    });
});

describe('2. debug mode in production', function () {
    test('debug mode is forced off in production', function () {
        config(['app.debug' => true]);

        bootApplicationAs('production');

        expect(config('app.debug'))->toBeFalse();
    });

    test('production error pages do not reveal exception details', function () {
        Route::middleware('web')->get('/_failing-route', fn () => throw new RuntimeException('database password is hunter2'));
        config(['app.debug' => true]);

        bootApplicationAs('production');

        $this->get('/_failing-route')
            ->assertServerError()
            ->assertDontSee('hunter2')
            ->assertDontSee('RuntimeException');
    });

    test('debug mode is left alone outside production', function () {
        config(['app.debug' => true]);

        bootApplicationAs('local');

        expect(config('app.debug'))->toBeTrue();
    });
});

describe('3. secure session cookies', function () {
    test('session cookies are marked secure in production by default', function () {
        config(['session.secure' => null]);

        bootApplicationAs('production');

        $sessionCookie = collect($this->get(route('home'))->headers->getCookies())
            ->first(fn ($cookie) => $cookie->getName() === config('session.cookie'));

        expect($sessionCookie->isSecure())->toBeTrue()
            ->and($sessionCookie->isHttpOnly())->toBeTrue();
    });

    test('an explicit SESSION_SECURE_COOKIE=false is respected', function () {
        config(['session.secure' => false]);

        bootApplicationAs('production');

        expect(config('session.secure'))->toBeFalse();
    });
});

describe('4. trusted hosts and proxies', function () {
    test('requests with a forged host header are rejected in production', function () {
        app()->detectEnvironment(fn () => 'production');

        $this->get('http://evil.example/')->assertBadRequest();
        $this->get('http://localhost/')->assertOk();
    });

    test('a spoofed X-Forwarded-For header cannot bypass the rate limit', function () {
        foreach (range(1, 5) as $attempt) {
            $this->withHeader('X-Forwarded-For', "203.0.113.{$attempt}")
                ->post(route('contact.store'), ContactMessage::factory()->raw())
                ->assertRedirect();
        }

        $this->withHeader('X-Forwarded-For', '203.0.113.99')
            ->post(route('contact.store'), ContactMessage::factory()->raw())
            ->assertTooManyRequests();
    });

    test('visitors behind a trusted proxy are rate limited individually', function () {
        config(['app.trusted_proxies' => '10.0.0.1']);
        bootApplicationAs('testing');

        foreach (range(1, 6) as $visitor) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
                ->withHeader('X-Forwarded-For', "203.0.113.{$visitor}")
                ->post(route('contact.store'), ContactMessage::factory()->raw())
                ->assertRedirect(route('contact'));
        }

        $this->assertDatabaseCount('contact_messages', 6);
    });
});

describe('5. contact form spam', function () {
    test('the form contains a hidden honeypot field', function () {
        $this->get(route('contact'))
            ->assertSee('name="website"', escape: false)
            ->assertSee('tabindex="-1"', escape: false);
    });

    test('submissions that fill the honeypot look successful but are not saved', function () {
        $this->post(route('contact.store'), [...ContactMessage::factory()->raw(), 'website' => 'https://spam.example'])
            ->assertRedirect(route('contact'))
            ->assertSessionHas('status');

        $this->assertDatabaseCount('contact_messages', 0);
    });

    test('a visitor can send at most 20 messages per day', function () {
        foreach (range(1, 4) as $minute) {
            foreach (range(1, 5) as $attempt) {
                $this->post(route('contact.store'), ContactMessage::factory()->raw())->assertRedirect();
            }

            $this->travel(1)->minutes();
        }

        $this->post(route('contact.store'), ContactMessage::factory()->raw())
            ->assertTooManyRequests();

        $this->assertDatabaseCount('contact_messages', 20);
    });
});

describe('6. open redirect in the language switcher', function () {
    test('switching language never redirects to another site', function () {
        $this->from('https://evil.example/phishing')
            ->post(route('locale.update', 'ar'))
            ->assertRedirect(route('home'));
    });

    test('switching language returns to the previous page on this site', function () {
        $this->from(route('contact'))
            ->post(route('locale.update', 'ar'))
            ->assertRedirect(route('contact'));
    });
});

describe('7. bidirectional text spoofing', function () {
    test('invisible text direction overrides are rejected', function (string $field) {
        $attributes = ContactMessage::factory()->raw([$field => "Invoice \u{202E}fdp.exe for review"]);

        $this->post(route('contact.store'), $attributes)
            ->assertSessionHasErrors([$field => "The {$field} field must not contain invisible text direction control characters."]);

        $this->assertDatabaseCount('contact_messages', 0);
    })->with(['name', 'subject', 'message']);

    test('the rejection message is translated to arabic', function () {
        $this->withSession(['locale' => 'ar'])
            ->post(route('contact.store'), ContactMessage::factory()->raw(['name' => "Sam\u{202E}"]))
            ->assertSessionHasErrors(['name' => 'يجب ألا يحتوي حقل الاسم على رموز تحكم مخفية في اتجاه النص.']);
    });

    test('ordinary direction marks used in mixed arabic and english text are allowed', function () {
        $attributes = ContactMessage::factory()->raw(['message' => "أريد عرضًا توضيحيًا لـ\u{200F} Nimbus Pro من فضلكم"]);

        $this->post(route('contact.store'), $attributes)->assertSessionHasNoErrors();

        $this->assertDatabaseHas('contact_messages', ['message' => $attributes['message']]);
    });
});

describe('8. language switching requires a csrf-protected post', function () {
    test('the language cannot be changed with a plain link', function () {
        $this->get('/locale/ar')->assertMethodNotAllowed();
    });

    test('the language switcher submits forms carrying a csrf token', function () {
        $this->get(route('home'))
            ->assertSee('<form method="POST" action="'.route('locale.update', 'ar').'">', escape: false)
            ->assertSee('name="_token"', escape: false);
    });
});
