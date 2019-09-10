<?php

declare(strict_types=1);

use EventEngine\DocumentStore\DocumentStore;
use Illuminate\Database\Migrations\Migration;

class BankAccountCollection extends Migration
{
    /** @var DocumentStore */
    private $documentStore;

    public function __construct()
    {
        $this->documentStore = app(DocumentStore::class);
    }

    /**
     * Run the migrations.
     */
    public function up() : void
    {
        $this->documentStore->addCollection('bank_accounts');
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        $this->documentStore->dropCollection('bank_accounts');
    }
}
