<?php

namespace App\Exceptions;

use Exception;

class JournalNotActiveException extends Exception
{
    public function __construct(string $message = "Cannot transfer to an inactive journal.")
    {
        parent::__construct($message);
    }

    /**
     * Render the exception for HTTP responses
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Journal not active',
                'message' => $this->getMessage(),
            ], 422);
        }

        return redirect()->back()
            ->withErrors(['journal' => $this->getMessage()]);
    }
}
