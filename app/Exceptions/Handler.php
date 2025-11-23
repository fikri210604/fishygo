<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // Session expired (CSRF token mismatch) -> inform and redirect to login
        if ($e instanceof TokenMismatchException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi telah berakhir. Silakan login ulang.'
                ], 419);
            }
            // Tampilkan halaman error 419 khusus dengan tombol login
            return response()->view('errors.419', [], 419);
        }

        if ($e instanceof ValidationException ||
            $e instanceof AuthenticationException ||
            $e instanceof AuthorizationException) {
            return parent::render($request, $e);
        }

        // If JSON or API request, return JSON error
        if ($request->expectsJson() || Str::startsWith($request->path(), 'api/')) {
            $message = config('app.debug') ? ($e->getMessage() ?: 'Server error') : 'Terjadi kesalahan pada server.';
            return response()->json([
                'message' => $message,
            ], $this->isHttpStatus($e) ? $e->getStatusCode() : 500);
        }

        // For typical web request: flash error and redirect back
        try {
            Log::error($e->getMessage(), ['exception' => $e]);
        } catch (Throwable $ignore) {}

        $msg = 'Terjadi kesalahan. Silakan coba lagi.';
        if (config('app.debug') && $e->getMessage()) {
            $msg .= ' ['.$e->getMessage().']';
        }

        if ($this->isHttpStatus($e) && in_array($e->getStatusCode(), [403, 404, 405], true)) {
            return parent::render($request, $e);
        }

        $back = back();
        if (!in_array(strtoupper($request->method()), ['GET', 'HEAD'])) {
            $back = $back->withInput();
        }
        return $back->with('error', $msg);
    }

    protected function isHttpStatus(Throwable $e): bool
    {
        return $e instanceof HttpExceptionInterface && method_exists($e, 'getStatusCode');
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        return redirect()->guest(route('login'))
            ->with('error', 'Sesi telah berakhir. Silakan login ulang.');
    }
}
