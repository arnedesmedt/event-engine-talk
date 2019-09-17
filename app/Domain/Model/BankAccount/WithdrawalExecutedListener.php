<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount;

use App\Domain\Api\Command;
use App\Domain\Api\Payload;
use App\Domain\Model\BankAccount\Event\WithdrawalExecuted;
use EventEngine\EventEngine;

final class WithdrawalExecutedListener
{
    /** @var EventEngine */
    private $eventEngine;

    public function __construct(EventEngine $eventEngine)
    {
        $this->eventEngine = $eventEngine;
    }

    public function __invoke(WithdrawalExecuted $withdrawalExecuted) : void
    {
        $this->eventEngine->dispatch(
            Command::RECEIVE_DEPOSIT,
            [
                Payload::ACCOUNT_ID => $withdrawalExecuted->destinationId()->toString(),
                Payload::SOURCE_ID => $withdrawalExecuted->accountId()->toString(),
                Payload::AMOUNT => $withdrawalExecuted->amount()->toInt(),
            ]
        );
    }
}
