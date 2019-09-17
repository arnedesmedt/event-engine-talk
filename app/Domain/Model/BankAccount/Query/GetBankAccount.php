<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount\Query;

use App\Domain\Model\BankAccount\ValueObject\AccountId;
use App\Domain\Resolver\Query;
use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;

final class GetBankAccount implements ImmutableRecord, Query
{
    use ImmutableRecordLogic;

    /** @var AccountId */
    private $accountId;

    public function accountId() : AccountId
    {
        return $this->accountId;
    }
}
