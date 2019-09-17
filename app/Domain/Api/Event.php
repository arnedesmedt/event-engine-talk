<?php

declare(strict_types=1);

namespace App\Domain\Api;

use App\Domain\Model\BankAccount\Event\DepositReceived;
use App\Domain\Model\BankAccount\Event\Registered;
use App\Domain\Model\BankAccount\Event\WithdrawalExecuted;
use App\Domain\Model\BankAccount\Event\WithdrawalRefused;
use EventEngine\Data\ImmutableRecord;
use EventEngine\EventEngine;
use EventEngine\EventEngineDescription;
use EventEngine\JsonSchema\JsonSchema;
use RuntimeException;
use function get_class;

final class Event implements EventEngineDescription
{
    public const BANK_ACCOUNT_REGISTERED = 'BankAccountRegistered';
    public const DEPOSIT_RECEIVED = 'DepositReceived';
    public const WITHDRAWAL_EXECUTED = 'WithdrawalExecuted';
    public const WITHDRAWAL_REFUSED = 'WithdrawalRefused';

    public const CLASS_MAP = [
        self::BANK_ACCOUNT_REGISTERED => Registered::class,
        self::DEPOSIT_RECEIVED => DepositReceived::class,
        self::WITHDRAWAL_EXECUTED => WithdrawalExecuted::class,
        self::WITHDRAWAL_REFUSED => WithdrawalRefused::class,
    ];

    public static function createFromNameAndPayload(string $name, array $payload) : ImmutableRecord
    {
        $className = self::CLASS_MAP[$name] ?? false;

        if ($className === false) {
            throw new RuntimeException(
                sprintf(
                    'Unknown event name: %s',
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
                'Unknown event'
            );
        }

        return (string) $name;
    }

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
