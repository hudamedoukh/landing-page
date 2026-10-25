<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->enforceProductionSafeguards();
        $this->configureTrustedProxies();
        $this->configureRateLimiting();
    }

    /**
     * Keep a misconfigured production environment from leaking internals.
     *
     * Debug mode is always turned off in production because its error pages
     * expose stack traces and environment values, and session cookies are
     * only sent over HTTPS unless SESSION_SECURE_COOKIE is explicitly set.
     */
    protected function enforceProductionSafeguards(): void
    {
        if (! $this->app->isProduction()) {
            return;
        }

        config(['app.debug' => false]);

        if (config('session.secure') === null) {
            config(['session.secure' => true]);
        }
    }

    /**
     * Trust the load balancers listed in TRUSTED_PROXIES so client IPs are correct.
     */
    protected function configureTrustedProxies(): void
    {
        $trustedProxies = config('app.trusted_proxies');

        if (blank($trustedProxies)) {
            return;
        }

        TrustProxies::at($trustedProxies === '*' ? '*' : array_map('trim', explode(',', $trustedProxies)));
    }

    /**
     * Limit how often a single visitor can submit the contact form.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('contact-form', fn (Request $request) => [
            Limit::perMinute(5)->by($request->ip()),
            Limit::perDay(20)->by($request->ip()),
        ]);
    }
}
