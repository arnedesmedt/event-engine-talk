<?php

declare(strict_types=1);

namespace App\Domain\Api;

use App\Domain\Model\BankAccount\GetBankAccount;
use App\Domain\Model\BankAccount\GetBankAccounts;
use EventEngine\EventEngine;
use EventEngine\EventEngineDescription;
use EventEngine\JsonSchema\JsonSchema;

final class Query implements EventEngineDescription
{
    public const GET_BANK_ACCOUNT = 'GetBankAccount';
    public const GET_BANK_ACCOUNTS = 'GetBankAccounts';

    public static function describe(EventEngine $eventEngine) : void
    {
        $eventEngine->registerQuery(
            self::GET_BANK_ACCOUNT,
            JsonSchema::object(
                [
                    'accountId' => JsonSchema::uuid(),
                ]
            )
        )
            ->resolveWith(GetBankAccount::class)
            ->setReturnType(JsonSchema::typeRef(Type::BANK_ACCOUNT));

        $eventEngine->registerQuery(
            self::GET_BANK_ACCOUNTS,
            JsonSchema::object(
                [],
                [
                    'owner' => JsonSchema::nullOr(JsonSchema::string()->withMinLength(1)),
                ]
            )
        )
            ->resolveWith(GetBankAccounts::class)
            ->setReturnType(
                JsonSchema::array(
                    JsonSchema::typeRef(Type::BANK_ACCOUNT)
                )
            );
    }
}
