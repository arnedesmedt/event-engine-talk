<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount\Event;

use App\Domain\Model\BankAccount\ValueObject\AccountId;
use App\Domain\Model\BankAccount\ValueObject\Name;
use App\Domain\Model\BankAccount\ValueObject\Owner;
use App\Domain\Model\Base\DomainEvent;
use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class Registered implements ImmutableRecord, DomainEvent
{
    use ImmutableRecordLogic;

    /** @var AccountId */
    private $accountId;

    /** @var Name */
    private $name;

    /** @var Owner */
    private $owner;

    public function accountId() : AccountId
    {
        return $this->accountId;
    }

    public function name() : Name
    {
        return $this->name;
    }

    public function owner() : Owner
    {
        return $this->owner;
    }
}
