<?php

namespace App\Exceptions;

use Exception;

class RecommendationApiException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report(): ?bool
    {
        return false;
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'error' => 'Recommendation service unavailable',
            'message' => $this->getMessage(),
        ], 503);
    }
}
