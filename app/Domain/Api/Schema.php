<?php

declare(strict_types=1);

namespace App\Domain\Api;

use EventEngine\JsonSchema\JsonSchema;
use EventEngine\JsonSchema\Type\IntType;
use EventEngine\JsonSchema\Type\StringType;
use EventEngine\JsonSchema\Type\UuidType;

final class Schema
{
    public static function accountId() : UuidType
    {
        return JsonSchema::uuid();
    }

    public static function accountName() : StringType
    {
        return JsonSchema::string()->withMinLength(2);
    }

    public static function accountOwner() : StringType
    {
        return JsonSchema::string()->withMinLength(3);
    }

    public static function transactionAmount() : IntType
    {
        return JsonSchema::integer()->withMinimum(1)->withMaximum(15000);
    }

    public static function accountAmount() : IntType
    {
        return JsonSchema::integer()->withMinimum(0)->withMaximum(500000);
    }
}
