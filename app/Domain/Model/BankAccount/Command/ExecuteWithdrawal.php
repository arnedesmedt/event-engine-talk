<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount\Command;

use App\Domain\Model\BankAccount\ValueObject\AccountId;
use App\Domain\Model\BankAccount\ValueObject\TransactionAmount;
use App\Domain\Model\Base\AggregateCommand;
use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class ExecuteWithdrawal implements ImmutableRecord, AggregateCommand
{
    use ImmutableRecordLogic;

    /** @var AccountId */
    private $accountId;

    /** @var AccountId */
    private $destinationId;

    /** @var TransactionAmount */
    private $amount;

    public function aggregateId() : string
    {
        return $this->accountId()->toString();
    }

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
