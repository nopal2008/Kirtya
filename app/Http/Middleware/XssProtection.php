<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XssProtection
{
    /**
     * Pola-pola XSS yang mencurigakan
     */
    private array $xssPatterns = [
        '/<script\b[^>]*>(.*?)<\/script>/is',
        '/<iframe\b[^>]*>(.*?)<\/iframe>/is',
        '/javascript:/i',
        '/on\w+\s*=/i', // onclick, onload, etc
        '/<embed\b[^>]*>/i',
        '/<object\b[^>]*>/i',
        '/vbscript:/i',
        '/<applet\b[^>]*>/i',
        '/<meta\b[^>]*>/i',
        '/<link\b[^>]*>/i',
        '/<style\b[^>]*>(.*?)<\/style>/is',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.security.xss_protection', true)) {
            return $next($request);
        }

        // Sanitize input
        $input = $request->all();
        $sanitized = $this->sanitizeInput($input, $request);
        $request->merge($sanitized);

        return $next($request);
    }

    /**
     * Sanitize input recursively
     */
    private function sanitizeInput(array $input, Request $request): array
    {
        $sanitized = [];

        foreach ($input as $key => $value) {
            if (is_string($value)) {
                // Deteksi XSS sebelum sanitize
                if ($this->containsXss($value)) {
                    \Log::warning('XSS attempt detected', [
                        'ip' => $request->ip(),
                        'url' => $request->fullUrl(),
                        'user_agent' => $request->userAgent(),
                        'input_key' => $key,
                    ]);
                }

                // Clean the input
                $sanitized[$key] = $this->cleanInput($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value, $request);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Check if string contains XSS patterns
     */
    private function containsXss(string $input): bool
    {
        foreach ($this->xssPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clean input dari karakter berbahaya
     */
    private function cleanInput(string $input): string
    {
        // Strip tags kecuali yang diizinkan
        $input = strip_tags($input);

        // Convert special characters
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $input;
    }
}
