<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount;

use App\Domain\Api\Command;
use App\Domain\Api\Payload;
use EventEngine\EventEngine;
use EventEngine\Messaging\Message;

final class WithdrawalExecutedListener
{
    /** @var EventEngine */
    private $eventEngine;

    public function __construct(EventEngine $eventEngine)
    {
        $this->eventEngine = $eventEngine;
    }

    public function __invoke(Message $withdrawalExecuted) : void
    {
        $this->eventEngine->dispatch(
            Command::RECEIVE_DEPOSIT,
            [
                Payload::ACCOUNT_ID => $withdrawalExecuted->get('destinationId'),
                Payload::SOURCE_ID => $withdrawalExecuted->get('accountId'),
                Payload::AMOUNT => $withdrawalExecuted->get('amount'),
            ]
        );
    }
}
