<?php

declare(strict_types=1);

namespace App\Domain\Api;

use App\Domain\Model\BankAccount\Command\ExecuteWithdrawal;
use App\Domain\Model\BankAccount\Command\ReceiveDeposit;
use App\Domain\Model\BankAccount\Command\Register;
use EventEngine\Data\ImmutableRecord;
use EventEngine\EventEngine;
use EventEngine\EventEngineDescription;
use EventEngine\JsonSchema\JsonSchema;
use RuntimeException;
use function get_class;

final class Command implements EventEngineDescription
{
    public const REGISTER_BANK_ACCOUNT = 'RegisterBankAccount';
    public const EXECUTE_WITHDRAWAL = 'ExecuteWithdrawal';
    public const RECEIVE_DEPOSIT = 'ReceiveDeposit';

    public const CLASS_MAP = [
        self::REGISTER_BANK_ACCOUNT => Register::class,
        self::EXECUTE_WITHDRAWAL => ExecuteWithdrawal::class,
        self::RECEIVE_DEPOSIT => ReceiveDeposit::class,
    ];

    public static function createFromNameAndPayload(string $name, array $payload) : ImmutableRecord
    {
        $className = self::CLASS_MAP[$name] ?? false;

        if ($className === false) {
            throw new RuntimeException(
                sprintf(
                    'Unknown command name: %s',
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
                'Unknown command'
            );
        }

        return (string) $name;
    }

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
