<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SQL Injection Protection
    |--------------------------------------------------------------------------
    |
    | Enable or disable SQL injection detection middleware
    |
    */
    'sql_injection_protection' => env('SECURITY_SQL_INJECTION_PROTECTION', true),

    /*
    |--------------------------------------------------------------------------
    | XSS Protection
    |--------------------------------------------------------------------------
    |
    | Enable or disable XSS (Cross-Site Scripting) protection middleware
    |
    */
    'xss_protection' => env('SECURITY_XSS_PROTECTION', true),

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS
    |--------------------------------------------------------------------------
    |
    | Force all requests to use HTTPS in production
    |
    */
    'force_https' => env('SECURITY_FORCE_HTTPS', true),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting to prevent brute force attacks
    |
    */
    'rate_limiting' => [
        'enabled' => env('SECURITY_RATE_LIMIT_ENABLED', true),

        // Login attempts
        'login' => [
            'max_attempts' => 5,
            'decay_minutes' => 15,
        ],

        // API requests
        'api' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],

        // Web requests
        'web' => [
            'max_attempts' => 100,
            'decay_minutes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configure security headers
    |
    */
    'headers' => [
        'hsts_max_age' => 31536000, // 1 year
        'csp_enabled' => true,
        'hide_server_info' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Blocked IPs
    |--------------------------------------------------------------------------
    |
    | List of IP addresses to block
    |
    */
    'blocked_ips' => env('SECURITY_BLOCKED_IPS', ''),

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    |
    | Enhanced session security settings
    |
    */
    'session' => [
        'regenerate_on_login' => true,
        'invalidate_on_logout' => true,
        'same_site' => 'lax', // lax, strict, none
    ],

];
