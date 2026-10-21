<?php

namespace App\Exceptions;

use App\Support\PersistentLogin;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof TokenMismatchException) {
            return $this->recoverAuthenticatedRequest($request, 419);
        }

        if ($this->isSessionReadFailure($e)) {
            return $this->recoverAuthenticatedRequest($request, 500);
        }

        if ($e instanceof AuthenticationException) {
            $user = PersistentLogin::rehydrate($request);
            if ($user) {
                return $this->redirectToCurrentOrBack($request, 401);
            }

            if ($this->isAjaxRequest($request)) {
                return response()->json([
                    'ok' => true,
                    'retry' => true,
                    'token' => csrf_token(),
                ], 401);
            }

            return redirect()->guest(route('login'));
        }

        if (
            $request->isMethod('GET')
            && !$request->boolean('_retry')
            && $this->isTransientServerError($e)
        ) {
            return redirect()->to($request->fullUrlWithQuery(['_retry' => 1]));
        }

        return parent::render($request, $e);
    }

    private function recoverAuthenticatedRequest(Request $request, int $status)
    {
        PersistentLogin::rehydrate($request);

        if ($request->hasSession()) {
            try {
                $request->session()->regenerateToken();
            } catch (Throwable $ignored) {
                //
            }
        }

        return $this->redirectToCurrentOrBack($request, $status);
    }

    private function redirectToCurrentOrBack(Request $request, int $status)
    {
        if ($this->isAjaxRequest($request)) {
            return response()->json([
                'ok' => true,
                'retry' => true,
                'token' => csrf_token(),
            ], $status);
        }

        if ($request->isMethod('GET')) {
            return redirect()->to($request->fullUrl());
        }

        return redirect()->back();
    }

    private function isAjaxRequest(Request $request): bool
    {
        return $request->expectsJson()
            || $request->ajax()
            || $request->wantsJson()
            || $request->header('X-Requested-With') === 'XMLHttpRequest'
            || str_contains((string) $request->header('Accept'), 'application/json');
    }

    private function isSessionReadFailure(Throwable $e): bool
    {
        $message = strtolower($e->getMessage() . ' ' . $e->getPrevious()?->getMessage());

        return str_contains($message, 'failed to start the session')
            || str_contains($message, 'session store not set on request')
            || str_contains($message, 'unable to create current session')
            || str_contains($message, 'session data is corrupted')
            || (str_contains($message, 'unserialize') && str_contains($message, 'session'))
            || (str_contains($message, 'session') && (
                str_contains($message, 'not readable')
                || str_contains($message, 'no such file')
                || str_contains($message, 'failed to open')
                || str_contains($message, 'permission denied')
            ));
    }

    private function isTransientServerError(Throwable $e): bool
    {
        if ($e instanceof HttpExceptionInterface && $e->getStatusCode() !== 500) {
            return false;
        }

        $message = strtolower($e->getMessage() . ' ' . $e->getPrevious()?->getMessage());

        return str_contains($message, 'has gone away')
            || str_contains($message, 'deadlock')
            || str_contains($message, 'lock wait timeout')
            || str_contains($message, 'unable to obtain lock')
            || (str_contains($message, 'session') && str_contains($message, 'lock'))
            || str_contains($message, 'sqlstate[40001]')
            || str_contains($message, 'sqlstate[hy000]');
    }
}
