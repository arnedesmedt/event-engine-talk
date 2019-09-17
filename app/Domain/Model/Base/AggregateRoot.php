<?php

declare(strict_types=1);

namespace App\Domain\Model\Base;

interface AggregateRoot
{
    public static function reconstituteFromHistory(DomainEvent ...$domainEvents) : self;

    /**
     * @param mixed[] $state
     */
    public static function reconstituteFromStateArray(array $state) : self;

    /**
     * @return DomainEvent[]
     */
    public function popRecordedEvents() : array;

    public function apply(DomainEvent $event) : void;

    /**
     * @return mixed[]
     */
    public function toArray() : array;

    // phpcs:ignore SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
    public function state();
}
