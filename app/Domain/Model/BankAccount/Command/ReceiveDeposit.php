<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount\Command;

use App\Domain\Model\BankAccount\ValueObject\AccountId;
use App\Domain\Model\BankAccount\ValueObject\TransactionAmount;
use App\Domain\Model\Base\AggregateCommand;
use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class ReceiveDeposit implements ImmutableRecord, AggregateCommand
{
    use ImmutableRecordLogic;

    /** @var AccountId */
    private $accountId;

    /** @var AccountId */
    private $sourceId;

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

    public function sourceId() : AccountId
    {
        return $this->sourceId;
    }

    public function amount() : TransactionAmount
    {
        return $this->amount;
    }
}
