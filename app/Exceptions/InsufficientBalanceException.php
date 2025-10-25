<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    public function __construct(
        private float $required,
        private float $available
    ) {
        parent::__construct(
            "Insufficient balance. Required: " . number_format($required, 2) .
            ", Available: " . number_format($available, 2)
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
                'error' => 'Insufficient balance',
                'message' => $this->getMessage(),
                'required_amount' => $this->required,
                'available_amount' => $this->available,
            ], 422);
        }

        return redirect()->back()
            ->withErrors(['amount' => $this->getMessage()])
            ->withInput();
    }

    /**
     * Get the required amount
     */
    public function getRequiredAmount(): float
    {
        return $this->required;
    }

    /**
     * Get the available amount
     */
    public function getAvailableAmount(): float
    {
        return $this->available;
    }
}
