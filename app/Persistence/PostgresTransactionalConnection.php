<?php

declare(strict_types=1);

namespace App\Persistence;

use EventEngine\Persistence\TransactionalConnection;
use PDO;

final class PostgresTransactionalConnection implements TransactionalConnection
{
    /** @var PDO */
    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function beginTransaction() : void
    {
        $this->connection->beginTransaction();
    }

    public function commit() : void
    {
        $this->connection->commit();
    }

    public function rollBack() : void
    {
        $this->connection->rollBack();
    }
}
