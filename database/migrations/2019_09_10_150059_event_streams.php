<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class EventStreams extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        /** @var PDO $pdo */
        $pdo = DB::connection()->getPdo();

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS event_streams (
                no BIGSERIAL,
                real_stream_name VARCHAR(150) NOT NULL,
                stream_name CHAR(41) NOT NULL,
                metadata JSONB,
                category VARCHAR(150),
                PRIMARY KEY (no),
                UNIQUE (stream_name)
            );'
        );

        $pdo->exec('CREATE INDEX on event_streams (category);');
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('event_streams');
    }
}
