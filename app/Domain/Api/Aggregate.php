<?php

declare(strict_types=1);

namespace App\Domain\Api;

use App\Domain\Model\BankAccount\BankAccount;
use EventEngine\EventEngine;
use EventEngine\EventEngineDescription;
use EventEngine\Runtime\Oop\FlavourHint;

final class Aggregate implements EventEngineDescription
{
    public const BANK_ACCOUNT = 'BankAccount';

    public const CLASS_MAP = [
        self::BANK_ACCOUNT => BankAccount::class,
    ];

    public static function describe(EventEngine $eventEngine) : void
    {
        $eventEngine->process(Command::REGISTER_BANK_ACCOUNT)
            ->withNew(self::BANK_ACCOUNT)
            ->identifiedBy(Payload::ACCOUNT_ID)
            ->handle([BankAccount::class, 'register'])
            ->recordThat(Event::BANK_ACCOUNT_REGISTERED)
            ->apply([FlavourHint::class, 'useAggregate'])
            ->storeStateIn('bank_accounts')
            ->storeEventsIn('bank_account_stream');

        $eventEngine->process(Command::EXECUTE_WITHDRAWAL)
            ->withExisting(self::BANK_ACCOUNT)
            ->identifiedBy(Payload::ACCOUNT_ID)
            ->handle([FlavourHint::class, 'useAggregate'])
            ->recordThat(Event::WITHDRAWAL_REFUSED)
            ->apply([FlavourHint::class, 'useAggregate'])
            ->orRecordThat(Event::WITHDRAWAL_EXECUTED)
            ->apply([FlavourHint::class, 'useAggregate']);

        $eventEngine->process(Command::RECEIVE_DEPOSIT)
            ->withExisting(self::BANK_ACCOUNT)
            ->identifiedBy(Payload::ACCOUNT_ID)
            ->handle([FlavourHint::class, 'useAggregate'])
            ->recordThat(Event::DEPOSIT_RECEIVED)
            ->apply([FlavourHint::class, 'useAggregate']);
    }
}
