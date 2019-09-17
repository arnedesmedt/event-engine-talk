<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount\Event;

use App\Domain\Model\BankAccount\ValueObject\AccountId;
use App\Domain\Model\BankAccount\ValueObject\TransactionAmount;
use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class WithdrawalExecuted implements ImmutableRecord
{
    use ImmutableRecordLogic;

    /** @var AccountId */
    private $accountId;

    /** @var AccountId */
    private $destinationId;

    /** @var TransactionAmount */
    private $amount;

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
}
