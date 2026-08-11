<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'pin',
        'token',
        'api_key',
        'secret',
        'access_token',
        'refresh_token',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log dengan informasi terbatas untuk keamanan
            if ($e instanceof QueryException) {
                // Jangan log query lengkap yang bisa berisi data sensitif
                \Log::error('Database error occurred', [
                    'code' => $e->getCode(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                    // Tidak include SQL query dan bindings
                ]);
                return; // Prevent default reporting
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Dalam mode production, sembunyikan detail error
        if (!config('app.debug')) {
            // Database errors - jangan expose query atau struktur database
            if ($e instanceof QueryException) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.',
                        'error' => 'DATABASE_ERROR'
                    ], 500);
                }

                return response()->view('errors.500', [
                    'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.'
                ], 500);
            }

            // Authentication errors
            if ($e instanceof AuthenticationException) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Tidak terautentikasi.',
                        'error' => 'UNAUTHENTICATED'
                    ], 401);
                }

                return redirect()->guest(route('login'));
            }

            // Validation errors - keep these as they're user-facing
            if ($e instanceof ValidationException) {
                return parent::render($request, $e);
            }

            // HTTP exceptions dengan pesan generic
            if ($e instanceof HttpException) {
                $statusCode = $e->getStatusCode();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $this->getGenericMessage($statusCode),
                        'error' => 'HTTP_ERROR'
                    ], $statusCode);
                }

                if (view()->exists("errors.{$statusCode}")) {
                    return response()->view("errors.{$statusCode}", [
                        'message' => $this->getGenericMessage($statusCode)
                    ], $statusCode);
                }
            }

            // Semua error lainnya
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan pada server. Silakan hubungi administrator.',
                    'error' => 'SERVER_ERROR'
                ], 500);
            }

            return response()->view('errors.500', [
                'message' => 'Terjadi kesalahan pada server. Silakan hubungi administrator.'
            ], 500);
        }

        return parent::render($request, $e);
    }

    /**
     * Get generic error message based on status code
     */
    private function getGenericMessage(int $statusCode): string
    {
        return match($statusCode) {
            400 => 'Permintaan tidak valid.',
            401 => 'Tidak terautentikasi.',
            403 => 'Akses ditolak.',
            404 => 'Halaman tidak ditemukan.',
            405 => 'Metode tidak diizinkan.',
            419 => 'Sesi telah kedaluwarsa. Silakan refresh halaman.',
            429 => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
            500 => 'Terjadi kesalahan pada server.',
            503 => 'Layanan sedang dalam maintenance.',
            default => 'Terjadi kesalahan.',
        };
    }
}
