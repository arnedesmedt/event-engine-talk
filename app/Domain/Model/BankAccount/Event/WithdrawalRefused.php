<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount\Event;

use App\Domain\Model\BankAccount\ValueObject\AccountAmount;
use App\Domain\Model\BankAccount\ValueObject\AccountId;
use App\Domain\Model\BankAccount\ValueObject\TransactionAmount;
use App\Domain\Model\Base\DomainEvent;
use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class WithdrawalRefused implements ImmutableRecord, DomainEvent
{
    use ImmutableRecordLogic;

    /** @var AccountId */
    private $accountId;

    /** @var AccountId */
    private $destinationId;

    /** @var TransactionAmount */
    private $amount;

    /** @var AccountAmount */
    private $accountAmount;

    public function accountId() : AccountId
    {
        return $this->accountId;
    }

    public function destinationId() : AccountId
    {
        return $this->destinationId;
    }

    public function amount() : TransactionAmount
    {
        return $this->amount;
    }

    public function accountAmount() : AccountAmount
    {
        return $this->accountAmount;
    }
}
