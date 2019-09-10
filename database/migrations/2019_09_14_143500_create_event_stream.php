<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;

class CreateEventStream extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        /** @var EventStore $eventStore */
        $eventStore = app(EventStore::class);
        $eventStore->create(new Stream(new StreamName('bank_account_stream'), new ArrayIterator()));
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('bank_account_stream');
    }
}
