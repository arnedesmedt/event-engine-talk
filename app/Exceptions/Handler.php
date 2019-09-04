<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     */
    public function report(Exception $exception) : void // phpcs:ignore SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly
    {
        parent::report($exception);
    }

    // phpcs:disable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     */
    public function render($request, Exception $exception) : Response // phpcs:ignore SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly
    {
        return parent::render($request, $exception);
    }
    // phpcs:enable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
}
