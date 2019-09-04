<?php

declare(strict_types=1);

namespace App\Domain\Api;

use App\Domain\Model\BankAccount\BankAccount;
use EventEngine\EventEngine;
use EventEngine\EventEngineDescription;

final class Aggregate implements EventEngineDescription
{
    public const BANK_ACCOUNT = 'BankAccount';

    public static function describe(EventEngine $eventEngine) : void
    {
        $eventEngine->process(Command::REGISTER_BANK_ACCOUNT)
            ->withNew(self::BANK_ACCOUNT)
            ->identifiedBy(Payload::ACCOUNT_ID)
            ->handle([BankAccount::class, 'register'])
            ->recordThat(Event::BANK_ACCOUNT_REGISTERED)
            ->apply([BankAccount::class, 'whenBankAccountRegistered'])
            ->storeStateIn('bank_accounts')
            ->storeEventsIn('bank_account_stream');

        $eventEngine->process(Command::EXECUTE_WITHDRAWAL)
            ->withExisting(self::BANK_ACCOUNT)
            ->identifiedBy(Payload::ACCOUNT_ID)
            ->handle([BankAccount::class, 'executeWithdrawal'])
            ->recordThat(Event::WITHDRAWAL_REFUSED)
            ->apply([BankAccount::class, 'whenWithdrawalRefused'])
            ->orRecordThat(Event::WITHDRAWAL_EXECUTED)
            ->apply([BankAccount::class, 'whenWithdrawalExecuted']);

        $eventEngine->process(Command::RECEIVE_DEPOSIT)
            ->withExisting(self::BANK_ACCOUNT)
            ->identifiedBy(Payload::ACCOUNT_ID)
            ->handle([BankAccount::class, 'receiveDeposit'])
            ->recordThat(Event::DEPOSIT_RECEIVED)
            ->apply([BankAccount::class, 'whenDepositReceived']);
    }
}
