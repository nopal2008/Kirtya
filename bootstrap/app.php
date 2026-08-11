<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan middleware Keamanan Siber - URUTAN PENTING!
        $middleware->web(append: [
            \App\Http\Middleware\ForceHttps::class,           // 1. Force HTTPS dulu
            \App\Http\Middleware\SqlInjectionProtection::class, // 2. SQL Injection protection
            \App\Http\Middleware\XssProtection::class,         // 3. XSS protection
            \App\Http\Middleware\SecurityHeaders::class,       // 4. Security headers terakhir
        ]);

        // Rate limiting untuk proteksi brute force (gunakan file/database, bukan redis)
        // $middleware->throttleWithRedis(); // Disabled - Redis not available on XAMPP

        // Daftarkan middleware alias Spatie Laravel-Permission
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Encrypt cookies untuk keamanan tambahan
        $middleware->encryptCookies(except: [
            // Cookies yang tidak perlu di-encrypt
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Exception handling sudah diatur di App\Exceptions\Handler
    })->create();
