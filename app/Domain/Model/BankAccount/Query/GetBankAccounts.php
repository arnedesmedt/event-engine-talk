<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount\Query;

use App\Domain\Model\BankAccount\ValueObject\Owner;
use App\Domain\Resolver\Query;
use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class GetBankAccounts implements ImmutableRecord, Query
{
    use ImmutableRecordLogic;

    /** @var Owner|null */
    private $owner;

    public function owner() : ?Owner
    {
        return $this->owner;
    }
}
