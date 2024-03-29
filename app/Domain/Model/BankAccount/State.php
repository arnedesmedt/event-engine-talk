<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount;

use App\Domain\Model\BankAccount\ValueObject\TransactionAmount;
use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class State implements ImmutableRecord
{
    use ImmutableRecordLogic;

    public const MINIMUM_TRESHOLD = 0;

    /** @var string */
    private $accountId;

    /** @var string */
    private $name;

    /** @var string */
    private $owner;

    /** @var int */
    private $amount = 0;

    public function accountId() : string
    {
        return $this->accountId;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function owner() : string
    {
        return $this->owner;
    }

    public function amount() : int
    {
        return $this->amount;
    }

    public function withWithdrawalExecuted(TransactionAmount $amount) : self
    {
        $copy = clone $this;
        $copy->amount -= $amount->toInt();

        return $copy;
    }

    public function withDepositReceived(TransactionAmount $amount) : self
    {
        $copy = clone $this;
        $copy->amount += $amount->toInt();

        return $copy;
    }

    public function isAmountBelowTresholdAfterWithdrawalOf(TransactionAmount $amount) : bool
    {
        return $this->amount - $amount->toInt() < self::MINIMUM_TRESHOLD;
    }
}
