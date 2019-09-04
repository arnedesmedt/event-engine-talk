<?php

declare(strict_types=1);

namespace App\Domain\Api;

use EventEngine\EventEngine;
use EventEngine\EventEngineDescription;
use EventEngine\JsonSchema\JsonSchema;

final class Event implements EventEngineDescription
{
    public const BANK_ACCOUNT_REGISTERED = 'BankAccountRegistered';
    public const DEPOSIT_RECEIVED = 'DepositReceived';
    public const WITHDRAWAL_EXECUTED = 'WithdrawalExecuted';
    public const WITHDRAWAL_REFUSED = 'WithdrawalRefused';

    public static function describe(EventEngine $eventEngine) : void
    {
        $eventEngine->registerEvent(
            self::BANK_ACCOUNT_REGISTERED,
            JsonSchema::object(
                [
                    Payload::ACCOUNT_ID => Schema::accountId(),
                    Payload::ACCOUNT_NAME => Schema::accountName(),
                    Payload::ACCOUNT_OWNER => Schema::accountOwner(),
                ]
            )
        );

        $eventEngine->registerEvent(
            self::WITHDRAWAL_EXECUTED,
            JsonSchema::object(
                [
                    Payload::ACCOUNT_ID => Schema::accountId(),
                    Payload::DESTINATION_ID => Schema::accountId(),
                    Payload::AMOUNT => Schema::transactionAmount(),
                ]
            )
        );

        $eventEngine->registerEvent(
            self::WITHDRAWAL_REFUSED,
            JsonSchema::object(
                [
                    Payload::ACCOUNT_ID => Schema::accountId(),
                    Payload::DESTINATION_ID => Schema::accountId(),
                    Payload::AMOUNT => Schema::transactionAmount(),
                    Payload::ACCOUNT_AMOUNT => Schema::accountAmount(),
                ]
            )
        );

        $eventEngine->registerEvent(
            self::DEPOSIT_RECEIVED,
            JsonSchema::object(
                [
                    Payload::ACCOUNT_ID => Schema::accountId(),
                    Payload::SOURCE_ID => Schema::accountId(),
                    Payload::AMOUNT => Schema::transactionAmount(),
                ]
            )
        );
    }
}
