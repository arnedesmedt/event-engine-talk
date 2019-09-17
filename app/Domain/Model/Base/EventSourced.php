<?php

declare(strict_types=1);

namespace App\Domain\Model\Base;

use EventEngine\Data\ImmutableRecord;
use ReflectionClass;
use RuntimeException;
use function array_pop;
use function explode;
use function get_class;

trait EventSourced
{
    /** @var DomainEvent[]    */
    private $recordedEvents = [];

    /** @var ImmutableRecord */
    private $state;

    /**
     * Rebuild the state based on the given domain events.
     */
    public static function reconstituteFromHistory(DomainEvent ...$domainEvents) : AggregateRoot
    {
        /** @var AggregateRoot $self */
        $self = new self();

        foreach ($domainEvents as $domainEvent) {
            $self->apply($domainEvent);
        }

        return $self;
    }

    /**
     * Rebuild the state based on a state object.
     */
    public static function reconstituteFromStateArray(array $state) : AggregateRoot
    {
        $stateClass = self::stateClass();

        $self = new self();
        $self->state = $stateClass::fromArray($state);

        return $self;
    }

    private static function stateClass() : string
    {
        $refObj = new ReflectionClass(self::class);

        $returnType = $refObj->getMethod('state')->getReturnType();

        if ($returnType === null) {
            throw new RuntimeException(
                sprintf(
                    'State method of aggregate %s must have a return type',
                    self::class
                )
            );
        }

        return $returnType->getName();
    }

    private function __construct()
    {
    }

    /**
     * Store new domain events until the popRecordedEvents method is triggered
     */
    public function recordThat(DomainEvent $event) : void
    {
        $this->recordedEvents[] = $event;
    }

    /**
     * Return all recorded events and clear them in the aggregate
     */
    public function popRecordedEvents() : array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    /**
     * Apply a domain event, so the state will be updated.
     */
    public function apply(DomainEvent $event) : void
    {
        $whenMethod = $this->deriveMethodNameFromEvent($event);

        if (! method_exists($this, $whenMethod)) {
            throw new RuntimeException(
                sprintf(
                    'Unable to apply event %s. Missing method %s in class %s',
                    get_class($event),
                    $whenMethod,
                    static::class
                )
            );
        }

        $this->{$whenMethod}($event);
    }

    /**
     * Get the apply method name
     */
    private function deriveMethodNameFromEvent(DomainEvent $event) : string
    {
        $nameParts = explode('\\', get_class($event));

        return 'when' . array_pop($nameParts);
    }

    /**
     * @return mixed[]
     */
    public function toArray() : array
    {
        return $this->state->toArray();
    }
}
