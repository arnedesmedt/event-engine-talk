<?php

declare(strict_types=1);

namespace App\Persistence;

use Iterator;
use Prooph\Common\Messaging\MessageConverter;
use Prooph\EventStore\Pdo\DefaultMessageConverter;
use Prooph\EventStore\Pdo\PersistenceStrategy;
use Prooph\EventStore\Pdo\Util\Json;
use Prooph\EventStore\Pdo\Util\PostgresHelper;
use Prooph\EventStore\StreamName;

final class PostgresSingleStreamStrategy implements PersistenceStrategy
{
    use PostgresHelper;

    /** @var MessageConverter */
    private $messageConverter;

    public function __construct(?MessageConverter $messageConverter = null)
    {
        $this->messageConverter = $messageConverter ?? new DefaultMessageConverter();
    }

    /**
     * @return string[]
     */
    public function createSchema(string $tableName) : array
    {
        $tableName = $this->quoteIdent($tableName);

        $statement = <<<EOT
CREATE TABLE $tableName (
    no BIGSERIAL,
    event_id UUID NOT NULL,
    event_name VARCHAR(100) NOT NULL,
    payload JSON NOT NULL,
    metadata JSONB NOT NULL,
    created_at TIMESTAMP(6) NOT NULL,
    PRIMARY KEY (no),
    CONSTRAINT aggregate_version_not_null CHECK ((metadata->>'_aggregate_version') IS NOT NULL),
    CONSTRAINT aggregate_type_not_null CHECK ((metadata->>'_aggregate_type') IS NOT NULL),
    CONSTRAINT aggregate_id_not_null CHECK ((metadata->>'_aggregate_id') IS NOT NULL),
    UNIQUE (event_id)
);
EOT;

        $index1 = <<<EOT
CREATE UNIQUE INDEX ON $tableName
((metadata->>'_aggregate_type'), (metadata->>'_aggregate_id'), (metadata->>'_aggregate_version'));
EOT;

        $index2 = <<<EOT
CREATE INDEX ON $tableName
((metadata->>'_aggregate_type'), (metadata->>'_aggregate_id'), no);
EOT;

        return [
            $statement,
            $index1,
            $index2,
        ];
    }

    /**
     * @return string[]
     */
    public function columnNames() : array
    {
        return [
            'event_id',
            'event_name',
            'payload',
            'metadata',
            'created_at',
        ];
    }

    /**
     * @return mixed[]
     */
    public function prepareData(Iterator $streamEvents) : array
    {
        $data = [];

        foreach ($streamEvents as $event) {
            $eventData = $this->messageConverter->convertToArray($event);

            $data[] = $eventData['uuid'];
            $data[] = $eventData['message_name'];
            $data[] = Json::encode($eventData['payload']);
            $data[] = Json::encode($eventData['metadata']);
            $data[] = $eventData['created_at']->format('Y-m-d\TH:i:s.u');
        }

        return $data;
    }

    public function generateTableName(StreamName $streamName) : string
    {
        $table = $streamName->toString();
        $schema = $this->extractSchema($table);

        if ($schema) {
            $table = $schema . '.' . $table;
        }

        return $table;
    }
}
