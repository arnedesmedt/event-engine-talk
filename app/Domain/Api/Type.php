<?php

declare(strict_types=1);

namespace App\Domain\Api;

use EventEngine\EventEngine;
use EventEngine\EventEngineDescription;
use EventEngine\JsonSchema\JsonSchema;

final class Type implements EventEngineDescription
{
    public const BANK_ACCOUNT = 'BankAccount';

    public static function describe(EventEngine $eventEngine) : void
    {
        $eventEngine->registerType(
            self::BANK_ACCOUNT,
            JsonSchema::object(
                [
                    'accountId' => JsonSchema::uuid(),
                    'name' => JsonSchema::string(),
                    'owner' => JsonSchema::string(),
                ]
            )
        );
    }
}
