<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount;

use App\Domain\Model\BankAccount\Command\ExecuteWithdrawal;
use App\Domain\Model\BankAccount\Command\ReceiveDeposit;
use App\Domain\Model\BankAccount\Command\Register;
use App\Domain\Model\BankAccount\Event\DepositReceived;
use App\Domain\Model\BankAccount\Event\Registered;
use App\Domain\Model\BankAccount\Event\WithdrawalExecuted;
use App\Domain\Model\BankAccount\Event\WithdrawalRefused;
use Traversable;

final class BankAccount
{
    public static function register(Register $registerBankAccount) : Traversable
    {
        yield Registered::fromArray($registerBankAccount->toArray());
    }

    public static function executeWithdrawal(State $state, ExecuteWithdrawal $executeWithdrawal) : Traversable
    {
        if ($state->isAmountBelowTresholdAfterWithdrawalOf($executeWithdrawal->amount())) {
            yield WithdrawalRefused::fromArray(
                array_merge(
                    ['accountAmount' => $state->amount()],
                    $executeWithdrawal->toArray()
                )
            );

            return;
        }

        yield WithdrawalExecuted::fromArray($executeWithdrawal->toArray());
    }

    public static function receiveDeposit(State $state, ReceiveDeposit $receiveDeposit) : Traversable
    {
        yield DepositReceived::fromArray($receiveDeposit->toArray());
    }

    public static function whenBankAccountRegistered(Registered $bankAccountRegistered) : State
    {
        return State::fromArray($bankAccountRegistered->toArray());
    }

    public static function whenWithdrawalExecuted(State $state, WithdrawalExecuted $withdrawalExecuted) : State
    {
        return $state->withWithdrawalExecuted($withdrawalExecuted->amount());
    }

    public static function whenWithdrawalRefused(State $state) : State
    {
        return $state;
    }

    public static function whenDepositReceived(State $state, DepositReceived $depositReceived) : State
    {
        return $state->withDepositReceived($depositReceived->amount());
    }
}
