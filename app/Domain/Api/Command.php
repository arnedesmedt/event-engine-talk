<?php

declare(strict_types=1);

namespace App\Domain\Api;

use EventEngine\EventEngine;
use EventEngine\EventEngineDescription;
use EventEngine\JsonSchema\JsonSchema;

final class Command implements EventEngineDescription
{
    public const REGISTER_BANK_ACCOUNT = 'RegisterBankAccount';
    public const EXECUTE_WITHDRAWAL = 'ExecuteWithdrawal';
    public const RECEIVE_DEPOSIT = 'ReceiveDeposit';

    public static function describe(EventEngine $eventEngine) : void
    {
        $eventEngine->registerCommand(
            self::REGISTER_BANK_ACCOUNT,
            JsonSchema::object(
                [
                    Payload::ACCOUNT_ID => Schema::accountId(),
                    Payload::ACCOUNT_NAME => Schema::accountName(),
                    Payload::ACCOUNT_OWNER => Schema::accountOwner(),
                ]
            )
        );

        $eventEngine->registerCommand(
            self::EXECUTE_WITHDRAWAL,
            JsonSchema::object(
                [
                    Payload::ACCOUNT_ID => Schema::accountId(),
                    Payload::DESTINATION_ID => Schema::accountId(),
                    Payload::AMOUNT => Schema::transactionAmount(),
                ]
            )
        );

        $eventEngine->registerCommand(
            self::RECEIVE_DEPOSIT,
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
