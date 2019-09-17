<?php

declare(strict_types=1);

namespace App\System\Flavour;

use App\Domain\Api\Command;
use App\Domain\Api\Event;
use App\Domain\Api\Query;
use App\Domain\Model\Base\AggregateCommand;
use App\Domain\Resolver\Resolver;
use EventEngine\Data\ImmutableRecord;
use EventEngine\Messaging\CommandDispatchResult;
use EventEngine\Messaging\Message;
use EventEngine\Messaging\MessageBag;
use EventEngine\Runtime\Functional\Port;
use RuntimeException;

final class ApplicationMessagePort implements Port
{
    public function deserialize(Message $message) : ImmutableRecord
    {
        switch ($message->messageType()) {
            case Message::TYPE_COMMAND:
                return Command::createFromNameAndPayload($message->messageName(), $message->payload());
                break;
            case Message::TYPE_EVENT:
                return Event::createFromNameAndPayload($message->messageName(), $message->payload());
                break;
            case Message::TYPE_QUERY:
                return Query::createFromNameAndPayload($message->messageName(), $message->payload());
                break;
        }
    }

    /**
     * @param mixed $customMessage
     *
     * @return array
     */
    public function serializePayload($customMessage) : array
    {
        if (is_array($customMessage)) {
            return $customMessage;
        }

        if (! ($customMessage instanceof ImmutableRecord)) {
            throw new RuntimeException(
                'Invalid message passed to serialize'
            );
        }

        return $customMessage->toArray();
    }

    /**
     * A message bag is a wrapper message that is used to send custom commands through Event Engine
     *
     * @param mixed $customCommand
     */
    public function decorateCommand($customCommand) : MessageBag
    {
        return new MessageBag(
            Command::nameOf($customCommand),
            MessageBag::TYPE_COMMAND,
            $customCommand
        );
    }

    /**
     * A message bag is a wrapper message that is used to send custom events through Event Engine
     *
     * @param mixed $customEvent
     */
    public function decorateEvent($customEvent) : MessageBag
    {
        return new MessageBag(
            Event::nameOf($customEvent),
            MessageBag::TYPE_EVENT,
            $customEvent
        );
    }

    /**
     * Custom created interface to retrieve the aggregate id
     *
     * @param mixed $command
     */
    public function getAggregateIdFromCommand(string $aggregateIdPayloadKey, $command) : string
    {
        if ($command instanceof AggregateCommand) {
            return $command->aggregateId();
        }

        throw new RuntimeException('Unknown aggregate id in command');
    }

    /**
     * @param mixed $customCommand
     * @param mixed $preProcessor  Custom preprocessor
     *
     * @return mixed|CommandDispatchResult Custom message or CommandDispatchResult
     */
    public function callCommandPreProcessor($customCommand, $preProcessor)
    {
        if (is_callable($preProcessor)) {
            return $preProcessor($customCommand);
        }

        throw new RuntimeException('Cannot call preprocessor');
    }

    /**
     * Commands returned by the controller are dispatched automatically
     *
     * @param mixed $customCommand
     * @param mixed $controller
     *
     * @return mixed[]|CommandDispatchResult|null Array of custom commands or null|CommandDispatchResult to indicate that no further action is required
     */
    public function callCommandController($customCommand, $controller)
    {
        if (is_callable($controller)) {
            return $controller($customCommand);
        }

        throw new RuntimeException('Cannot call command controller');
    }

    /**
     * Add some context to the aggregate handlers
     *
     * @param mixed $customCommand
     * @param mixed $contextProvider
     *
     * @return mixed
     */
    public function callContextProvider($customCommand, $contextProvider)
    {
        if (is_callable($contextProvider)) {
            return $contextProvider($customCommand);
        }

        throw new RuntimeException('Cannot call context provider');
    }


    /**
     * @param mixed $customQuery
     * @param mixed $resolver
     *
     * @return mixed
     */
    public function callResolver($customQuery, $resolver)
    {
        if (! $resolver instanceof Resolver) {
            throw new RuntimeException('Unsupported resolver');
        }

        return $resolver->resolve($customQuery);
    }
}
