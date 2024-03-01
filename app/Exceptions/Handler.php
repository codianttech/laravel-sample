<?php

namespace App\Exceptions;

use App\Jobs\MicrosoftTeamsNotificationJob;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use PDOException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [

    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        CustomException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
    public function register(): void
    {
        $this->reportable(
            fn (Throwable $e) => true
        );
    }

    /**
     * Method render
     *
     * @param Illuminate\Http\Request $request
     * @param Throwable               $exception
     *
     * @return JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {

        $appEnv = config('app.env');

        $message = $exception->getMessage();
        $code = 400;

        // Validation error
        if ($exception instanceof ValidationException) {
            return parent::render($request, $exception);
        }
        // Show custom message for bad request
        if (0 == $exception->getCode()) {
            $message = Lang::get('message.error.exception.server');
        }
        // Show custom validation message
        if ($exception instanceof CustomException) {
            $message = $exception->getMessage();
        }
        // Set message for database error
        if ($exception instanceof PDOException) {
            $message = Lang::get('message.error.exception.server');
        }
        // Set message for database error
        if ($exception instanceof NotFoundHttpException) {
            $code = 404;
            $message = Lang::get('message.error.exception.page_not_found');
        }
        // Set message for authorization exception
        if ($exception instanceof AuthorizationException) {
            $code = 403;
            $message = Lang::get('message.error.exception.not_allowed');
        }
        // Set message for model not found exception
        if ($exception instanceof ModelNotFoundException) {
            $code = 404;
            $message = Lang::get('message.error.exception.model_not_found');
        }

        if (! ($exception instanceof CustomException) && config('services.microsoftTeams.notificationEnable')) {
            $custom_message = '<br/> <b> Message </b> : ' . $exception->getMessage();
            $custom_message .= '<br/> <b> Route Name </b> : ' . @Route::currentRouteName();
            $custom_message .= '<br/> <b> Code </b> : ' . $exception->getCode();
            $custom_message .= '<br/> <b> File </b> : ' . $exception->getFile();
            $custom_message .= '<br/> <b> Line </b> : ' . $exception->getLine();
            $re = config('constants.regex_validation.error_styling');
            $subst = '<br/> $1';
            $errorString = $exception->getTraceAsString();
            $result = preg_replace($re, $subst, $errorString);
            $custom_message .= '<br/> <b> TraceAsString </b> : ' . $result;
            dispatch(new MicrosoftTeamsNotificationJob($custom_message));
        }

        // Send response for API and ajax call.
        if ($request->ajax() || $request->wantsJson() || $request->is('api/*')) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => $message,
                ],
                $code
            );
        }
        // Show custom message if app env is production.
        if ('production' == $appEnv) {
            if ($exception instanceof NotFoundHttpException) {
                return response()->view('errors.default', ['errorCode' => 404], 404);
            }
            if ($exception instanceof ThrottleRequestsException) {
                return response()->view('errors.default', ['errorCode' => 429], 429);
            }
            if ($exception instanceof AuthenticationException) {
                if ($request->is('admin/*')) {
                    return redirect()->route('admin.login');
                }
            } else {
                return response()->view('errors.default', ['errorCode' => 500], 500);
            }
        } else {
            if ($exception instanceof NotFoundHttpException) {
                return response()->view('errors.default', ['errorCode' => 404], 404);
            }

            return parent::render($request, $exception);
        }

    }
}
