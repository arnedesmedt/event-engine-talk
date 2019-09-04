<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount;

use App\Domain\Api\Event;
use EventEngine\Messaging\Message;
use Traversable;

final class BankAccount
{
    public static function register(Message $registerBankAccount) : Traversable
    {
        yield [Event::BANK_ACCOUNT_REGISTERED, $registerBankAccount->payload()];
    }

    public static function executeWithdrawal(State $state, Message $executeWithdrawal) : Traversable
    {
        if ($state->isAmountBelowTresholdAfterWithdrawalOf($executeWithdrawal->get('amount'))) {
            yield [
                Event::WITHDRAWAL_REFUSED,
                array_merge(
                    ['accountAmount' => $state->amount()],
                    $executeWithdrawal->payload()
                ),
            ];

            return;
        }

        yield [Event::WITHDRAWAL_EXECUTED, $executeWithdrawal->payload()];
    }

    public static function receiveDeposit(State $state, Message $receiveDeposit) : Traversable
    {
        yield [Event::DEPOSIT_RECEIVED, $receiveDeposit->payload()];
    }

    public static function whenBankAccountRegistered(Message $bankAccountRegistered) : State
    {
        return State::fromArray($bankAccountRegistered->payload());
    }

    public static function whenWithdrawalExecuted(State $state, Message $withdrawalExecuted) : State
    {
        return $state->withWithdrawalExecuted($withdrawalExecuted->get('amount'));
    }

    public static function whenWithdrawalRefused(State $state) : State
    {
        return $state;
    }

    public static function whenDepositReceived(State $state, Message $depositReceived) : State
    {
        return $state->withDepositReceived($depositReceived->get('amount'));
    }
}
