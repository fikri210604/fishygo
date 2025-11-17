<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Throwable;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function errorMessage(Throwable $e, ?string $fallback = null): string
    {
        $message = $fallback ?? 'Terjadi kesalahan. Silakan coba lagi.';
        if (config('app.debug')) {
            $message .= ' [' . $e->getMessage() . ']';
        }
        return $message;
    }

    protected function logException(Throwable $e, array $context = []): void
    {
        try {
            Log::error($e->getMessage(), array_merge($context, ['exception' => $e]));
        } catch (Throwable $ignored) {
            // ignore logging failures
        }
    }
}
