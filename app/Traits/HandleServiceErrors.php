<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Throwable;

trait HandleServiceErrors
{
    /**
     * Handle exceptions and return a standard array structure
     */
    protected function handleServiceException(Throwable $e, string $context = 'Service Error'): array
    {
        Log::error($context.': '.$e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);

        return [
            'success' => false,
            'message' => 'Something went wrong, please try again later.',
            'code'    => 500,
            'data'    => []
        ];
    }
}
