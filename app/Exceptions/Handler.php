<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle foreign key constraint violations globally so all forms
        // show a friendly message instead of a raw SQL error.
        $this->renderable(function (Throwable $e, $request) {
            if ($e instanceof QueryException) {
                $errorCode = $e->errorInfo[1] ?? null;

                // MySQL foreign key constraint error code
                if ($errorCode === 1451 || $errorCode === 1452) {
                    $message = 'This record is used in another form and cannot be deleted. '
                             . 'Please remove its usage in other forms before deleting.';

                    // For normal web requests, redirect back with a warning message
                    if (!$request->expectsJson()) {
                        return redirect()->back()->with('error', $message);
                    }

                    // For API / JSON requests, return a JSON error response
                    return response()->json([
                        'message' => $message,
                    ], 409);
                }
            }

            return null;
        });
    }
}
