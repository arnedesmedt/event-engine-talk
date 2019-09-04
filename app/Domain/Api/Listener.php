<?php

declare(strict_types=1);

namespace App\Domain\Api;

use App\Domain\Model\BankAccount\WithdrawalExecutedListener;
use EventEngine\EventEngine;
use EventEngine\EventEngineDescription;

final class Listener implements EventEngineDescription
{
    public static function describe(EventEngine $eventEngine) : void
    {
        $eventEngine->on(Event::WITHDRAWAL_EXECUTED, WithdrawalExecutedListener::class);
    }
}
