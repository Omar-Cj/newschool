<?php

namespace App\Exceptions;

use Exception;

class JournalNotFullyTransferredException extends Exception
{
    public function __construct(string $message = null, private ?float $progressPercentage = null)
    {
        parent::__construct(
            $message ?? "Journal cannot be closed. It is not fully transferred yet."
        );
    }

    /**
     * Render the exception for HTTP responses
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Journal not fully transferred',
                'message' => $this->getMessage(),
                'progress_percentage' => $this->progressPercentage,
            ], 422);
        }

        return redirect()->back()
            ->withErrors(['journal' => $this->getMessage()]);
    }

    /**
     * Get the progress percentage
     */
    public function getProgressPercentage(): ?float
    {
        return $this->progressPercentage;
    }
}
