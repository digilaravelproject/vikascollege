<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler; // <-- FIX 1 YAHAN HAI
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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

        // Authorization Exception ko handle karein
        $this->renderable(function (AuthorizationException $e, $request) {

            // Check karein ki request admin panel se aa rahi hai
            if ($request->is('admin/*') && !$request->wantsJson()) {

                // <-- FIX 2 YAHAN HAI ('permission_error')
                return redirect()->back()->with('permission_error', 'You do not have permission to perform this action.');
            }
        });
    }
}
