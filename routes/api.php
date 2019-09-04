<?php

declare(strict_types=1);

use App\Domain\Api\Command;
use App\Domain\Api\Event;
use App\Domain\Api\Query;
use EventEngine\EventEngine;
use EventEngine\Messaging\CommandDispatchResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post(
    '/bank-account',
    static function (Request $request) : void {
        app(EventEngine::class)
            ->dispatch(
                Command::REGISTER_BANK_ACCOUNT,
                $request->toArray()
            );
    }
);

Route::get(
    '/bank-account/{accountId}',
    static function (Request $request) {
        /** @var \Illuminate\Routing\Route $route */
        $route = $request->route();
        $result = app(EventEngine::class)
            ->dispatch(
                Query::GET_BANK_ACCOUNT,
                $route->parameters()
            );

        return JsonResponse::create($result);
    }
);

Route::get(
    '/bank-account',
    static function (Request $request) {
        $result = app(EventEngine::class)
            ->dispatch(
                Query::GET_BANK_ACCOUNTS,
                $request->query()
            );

        return JsonResponse::create($result);
    }
);

Route::patch(
    '/bank-account/{accountId}/withdrawal',
    static function (Request $request) : void {
        /** @var \Illuminate\Routing\Route $route */
        $route = $request->route();
        /** @var CommandDispatchResult $result */
        $result = app(EventEngine::class)
            ->dispatch(
                Command::EXECUTE_WITHDRAWAL,
                array_merge($route->parameters(), $request->toArray())
            );

        if ($result->recordedEvents()[0]->messageName() === Event::WITHDRAWAL_REFUSED) {
            throw new DomainException(
                'Could not execute withdrawal'
            );
        }
    }
);

Route::patch(
    '/bank-account/{accountId}/deposit',
    static function (Request $request) : void {
        /** @var \Illuminate\Routing\Route $route */
        $route = $request->route();
        app(EventEngine::class)
            ->dispatch(
                Command::RECEIVE_DEPOSIT,
                array_merge($route->parameters(), $request->toArray())
            );
    }
);
