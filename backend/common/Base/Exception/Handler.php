<?php

namespace Common\Base\Exception;

use Throwable;

use Common\Packages\OneTimePassword\OneTimePasswordVerificationException;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ExceptionWithoutReport::class,
        OneTimePasswordVerificationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws \Common\Base\Exception\Exception
     */
    public function report(Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            Log::warning('request validation error', $exception->errors());
            return;
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response|JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof OneTimePasswordVerificationException) {
            return \Common\Base\Http\Response::response(
                $exception->getHttpCode(),
                $exception->toResponse(),
                $exception->getHeaders()
            );
        }

        if ($exception instanceof \Common\Base\Exception\Exception) {
            $defaultPayload = ['message' => $exception->getMessage()];

            if ($exception->getExceptionCode() !== null) {
                $defaultPayload['code'] = $exception->getExceptionCode();
            }

            return \Common\Base\Http\Response::response(
                $exception->getHttpCode(),
                $exception->getPayload() ?? $defaultPayload,
                $exception->getHeaders()
            );
        }

        if ($exception instanceof ValidationException) {
            return response()->json($exception->errors(), Response::HTTP_BAD_REQUEST, [], JSON_UNESCAPED_UNICODE);
        }

        if (!app()->environment('production')) {
            return parent::render($request, $exception);
        }

        return response()->json(
            [
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'internal server error',
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
