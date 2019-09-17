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
use App\Domain\Model\Base\AggregateRoot;
use App\Domain\Model\Base\EventSourced;

final class BankAccount implements AggregateRoot
{
    use EventSourced;

    /** @var State */
    private $state;

    public static function reconstituteFromStateArray(array $state) : AggregateRoot
    {
        $self = new self();
        $self->state = State::fromArray($state);

        return $self;
    }

    public static function register(Register $registerBankAccount) : AggregateRoot
    {
        $self = new self();
        $self->recordThat(Registered::fromArray($registerBankAccount->toArray()));

        return $self;
    }

    public function executeWithdrawal(ExecuteWithdrawal $executeWithdrawal) : void
    {
        if ($this->state()->isAmountBelowTresholdAfterWithdrawalOf($executeWithdrawal->amount())) {
            $this->recordThat(
                WithdrawalRefused::fromArray(
                    array_merge(
                        ['accountAmount' => $this->state()->amount()],
                        $executeWithdrawal->toArray()
                    )
                )
            );

            return;
        }

        $this->recordThat(WithdrawalExecuted::fromArray($executeWithdrawal->toArray()));
    }

    public function receiveDeposit(ReceiveDeposit $receiveDeposit) : void
    {
        $this->recordThat(DepositReceived::fromArray($receiveDeposit->toArray()));
    }

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
    private function whenRegistered(Registered $bankAccountRegistered) : void
    {
        $this->state = State::fromArray($bankAccountRegistered->toArray());
    }

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
    private function whenWithdrawalExecuted(WithdrawalExecuted $withdrawalExecuted) : void
    {
        $this->state = $this->state()->withWithdrawalExecuted($withdrawalExecuted->amount());
    }

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
    private function whenWithdrawalRefused() : void
    {
        return;
    }

    // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
    private function whenDepositReceived(DepositReceived $depositReceived) : void
    {
        $this->state = $this->state()->withDepositReceived($depositReceived->amount());
    }

    public function state() : State
    {
        return $this->state;
    }

    public function toArray() : array
    {
        return $this->state->toArray();
    }
}
