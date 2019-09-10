<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class ProjectionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        /** @var PDO $pdo */
        $pdo = DB::connection()->getPdo();

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS projections (
                no BIGSERIAL,
                name VARCHAR(150) NOT NULL,
                position JSONB,
                state JSONB,
                status VARCHAR(28) NOT NULL,
                locked_until CHAR(26),
                PRIMARY KEY (no),
                UNIQUE (name)
            );'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('projections');
    }
}
