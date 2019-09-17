<?php

declare(strict_types=1);

namespace App\System\Flavour;

use App\Domain\Api\Aggregate;
use App\Domain\Model\Base\AggregateRoot;
use EventEngine\Runtime\Oop\Port;
use RuntimeException;
use function array_pop;
use function explode;
use function get_class;
use function lcfirst;

final class EventSourcedAggregatePort implements Port
{
    /**
     * Creates a new aggregate
     *
     * @param callable $aggregateFactory => See the handle function of newly created Aggregates in Aggregate.php
     * @param mixed $customCommand
     * @param mixed $contextServices
     *
     * @return mixed Created aggregate
     */
    public function callAggregateFactory(string $aggregateType, callable $aggregateFactory, $customCommand, ...$contextServices) // phpcs:ignore SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
    {
        return $aggregateFactory($customCommand, ...$contextServices);
    }

    /**
     * @param mixed $aggregate
     * @param mixed $customCommand
     * @param mixed $contextServices
     */
    public function callAggregateWithCommand($aggregate, $customCommand, ...$contextServices) : void // phpcs:ignore SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
    {
        $commandNameParts = explode('\\', get_class($customCommand));
        $handlingMethod = lcfirst(array_pop($commandNameParts));

        $aggregate->{$handlingMethod}($customCommand, ...$contextServices);
    }

    /**
     * @param mixed $aggregate
     *
     * @return array of custom events
     */
    public function popRecordedEvents($aggregate) : array
    {
        if (! $aggregate instanceof AggregateRoot) {
            throw new RuntimeException(
                sprintf(
                    'Cannot apply event. Given aggregate is not an instance of %s. Got %s',
                    AggregateRoot::class,
                    is_object($aggregate) ? get_class($aggregate) : gettype($aggregate)
                )
            );
        }

        return $aggregate->popRecordedEvents();
    }

    /**
     * @param mixed $aggregate
     * @param mixed $customEvent
     */
    public function applyEvent($aggregate, $customEvent) : void
    {
        if (! $aggregate instanceof AggregateRoot) {
            throw new RuntimeException(
                sprintf(
                    'Cannot apply event. Given aggregate is not an instance of %s. Got %s',
                    AggregateRoot::class,
                    is_object($aggregate) ? get_class($aggregate) : gettype($aggregate)
                )
            );
        }

        $aggregate->apply($customEvent);
    }

    /**
     * @param mixed $aggregate
     *
     * @return array
     */
    public function serializeAggregate($aggregate) : array
    {
        if (! $aggregate instanceof AggregateRoot) {
            throw new RuntimeException(
                sprintf(
                    'Cannot serialize aggregate. Given aggregate is not an instance of %s. Got %s',
                    AggregateRoot::class,
                    (is_object($aggregate)? get_class($aggregate) : gettype($aggregate))
                )
            );
        }

        return $aggregate->toArray();
    }

    /**
     * Rebuild the aggregate state, based on the given events
     *
     * @param iterable $events history
     *
     * @return mixed Aggregate instance
     */
    public function reconstituteAggregate(string $aggregateType, iterable $events)
    {
        /** @var AggregateRoot $aggregateClass */
        $aggregateClass = $this->aggregateClassByType($aggregateType);

        return $aggregateClass::reconstituteFromHistory(...$events);
    }

    /**
     * @param array $state
     *
     * @return mixed Aggregate instance
     */
    public function reconstituteAggregateFromStateArray(string $aggregateType, array $state, int $version)
    {
        /** @var AggregateRoot $aggregateClass */
        $aggregateClass = $this->aggregateClassByType($aggregateType);

        return $aggregateClass::reconstituteFromStateArray($state);
    }

    private function aggregateClassByType(string $aggregateType) : string
    {
        $aggregateClass = Aggregate::CLASS_MAP[$aggregateType] ?? '';

        if ($aggregateClass) {
            return $aggregateClass;
        }

        throw new RuntimeException(
            sprintf(
                'Unknown aggregate type %s',
                $aggregateType
            )
        );
    }
}
