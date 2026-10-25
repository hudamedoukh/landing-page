<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Add browser security headers to every web response.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Vite::useCspNonce();

        header_remove('X-Powered-By');

        $response = $next($request);

        $response->headers->remove('X-Powered-By');
        $response->headers->add([
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ]);

        if (! ($response->isServerError() && config('app.debug'))) {
            $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy($nonce));
        }

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    /**
     * Build a policy that only runs scripts carrying this request's nonce.
     *
     * Inline styles stay allowed because the font loader and Laravel's error
     * pages emit them, and the Vite dev server is allowed while it is running.
     */
    protected function contentSecurityPolicy(string $nonce): string
    {
        $viteDevServer = Vite::isRunningHot() ? trim(file_get_contents(Vite::hotFile())) : null;
        $viteDevServerSocket = $viteDevServer ? preg_replace('/^http/', 'ws', $viteDevServer) : null;

        $directives = [
            'default-src' => ["'self'"],
            'script-src' => ["'self'", "'nonce-{$nonce}'", $viteDevServer],
            'style-src' => ["'self'", "'unsafe-inline'", $viteDevServer],
            'font-src' => ["'self'", $viteDevServer],
            'img-src' => ["'self'", 'data:'],
            'connect-src' => ["'self'", $viteDevServer, $viteDevServerSocket],
            'object-src' => ["'none'"],
            'base-uri' => ["'self'"],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'none'"],
        ];

        return collect($directives)
            ->map(fn (array $sources, string $directive) => $directive.' '.implode(' ', array_filter($sources)))
            ->implode('; ');
    }
}
