<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    // phpcs:disable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint,SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request) : string
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
    // phpcs:enable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint,SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
}
