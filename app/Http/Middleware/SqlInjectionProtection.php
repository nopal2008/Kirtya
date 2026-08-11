<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SqlInjectionProtection
{
    /**
     * Pola-pola yang mencurigakan untuk SQL Injection
     */
    private array $sqlPatterns = [
        '/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|EXECUTE|UNION|DECLARE)\b)/i',
        '/(--|\#|\/\*|\*\/)/i',
        '/(\bOR\b.*=.*|AND.*=.*)/i',
        '/(\b(CONCAT|CHAR|VARCHAR|NVARCHAR)\b.*\()/i',
        '/(;|\||&&)/i',
        '/(\bxp_cmdshell\b)/i',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.security.sql_injection_protection', true)) {
            return $next($request);
        }

        // Periksa semua input
        foreach ($request->all() as $key => $value) {
            if (is_string($value)) {
                if ($this->containsSqlInjection($value)) {
                    \Log::warning('SQL Injection attempt detected', [
                        'ip' => $request->ip(),
                        'url' => $request->fullUrl(),
                        'user_agent' => $request->userAgent(),
                        'input_key' => $key,
                    ]);

                    abort(403, 'Forbidden: Suspicious input detected.');
                }
            } elseif (is_array($value)) {
                $this->checkArrayForSqlInjection($value, $request, $key);
            }
        }

        return $next($request);
    }

    /**
     * Check array recursively for SQL injection
     */
    private function checkArrayForSqlInjection(array $data, Request $request, string $parentKey = ''): void
    {
        foreach ($data as $key => $value) {
            $fullKey = $parentKey ? "{$parentKey}.{$key}" : $key;

            if (is_string($value)) {
                if ($this->containsSqlInjection($value)) {
                    \Log::warning('SQL Injection attempt detected in array', [
                        'ip' => $request->ip(),
                        'url' => $request->fullUrl(),
                        'user_agent' => $request->userAgent(),
                        'input_key' => $fullKey,
                    ]);

                    abort(403, 'Forbidden: Suspicious input detected.');
                }
            } elseif (is_array($value)) {
                $this->checkArrayForSqlInjection($value, $request, $fullKey);
            }
        }
    }

    /**
     * Check if string contains SQL injection patterns
     */
    private function containsSqlInjection(string $input): bool
    {
        // Skip jika string terlalu pendek atau kosong
        if (strlen(trim($input)) < 3) {
            return false;
        }

        foreach ($this->sqlPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }
}
