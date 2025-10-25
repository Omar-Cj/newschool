<?php

namespace App\Exceptions;

use Exception;

class TransferAlreadyApprovedException extends Exception
{
    public function __construct(string $message = "Transfer is already approved.")
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
                'error' => 'Transfer already approved',
                'message' => $this->getMessage(),
            ], 422);
        }

        return redirect()->back()
            ->withErrors(['transfer' => $this->getMessage()]);
    }
}
