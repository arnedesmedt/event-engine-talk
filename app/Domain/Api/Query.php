<?php

declare(strict_types=1);

namespace App\Domain\Api;

use App\Domain\Model\BankAccount\GetBankAccount;
use App\Domain\Model\BankAccount\GetBankAccounts;
use App\Domain\Model\BankAccount\Query\GetBankAccount as GetBankAccountQuery;
use App\Domain\Model\BankAccount\Query\GetBankAccounts as GetBankAccountsQuery;
use EventEngine\Data\ImmutableRecord;
use EventEngine\EventEngine;
use EventEngine\EventEngineDescription;
use EventEngine\JsonSchema\JsonSchema;
use RuntimeException;
use function get_class;

final class Query implements EventEngineDescription
{
    public const GET_BANK_ACCOUNT = 'GetBankAccount';
    public const GET_BANK_ACCOUNTS = 'GetBankAccounts';

    public const CLASS_MAP = [
        self::GET_BANK_ACCOUNT => GetBankAccountQuery::class,
        self::GET_BANK_ACCOUNTS => GetBankAccountsQuery::class,
    ];

    public static function createFromNameAndPayload(string $name, array $payload) : ImmutableRecord
    {
        $className = self::CLASS_MAP[$name] ?? false;

        if ($className === false) {
            throw new RuntimeException(
                sprintf(
                    'Unknown query name: %s',
                    $name
                )
            );
        }

        /** @var ImmutableRecord $className */
        return $className::fromArray($payload);
    }

    public static function nameOf(ImmutableRecord $message) : string
    {
        $name = array_search(get_class($message), self::CLASS_MAP);

        if ($name === false) {
            throw new RuntimeException(
                'Unknown query'
            );
        }

        return (string) $name;
    }

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
