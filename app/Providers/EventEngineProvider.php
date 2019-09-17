<?php

declare(strict_types=1);

namespace App\Providers;

use App;
use App\Domain\Api\Aggregate;
use App\Domain\Api\Command;
use App\Domain\Api\Event;
use App\Domain\Api\Listener;
use App\Domain\Api\Query;
use App\Domain\Api\Type;
use App\Persistence\PostgresSingleStreamStrategy;
use App\Persistence\PostgresTransactionalConnection;
use App\System\Flavour\ApplicationMessagePort;
use DB;
use EventEngine\Data\ImmutableRecordDataConverter;
use EventEngine\DocumentStore\DocumentStore;
use EventEngine\DocumentStore\Postgres\PostgresDocumentStore;
use EventEngine\EventEngine;
use EventEngine\EventStore\EventStore;
use EventEngine\JsonSchema\OpisJsonSchema;
use EventEngine\Logger\SimpleMessageEngine;
use EventEngine\Persistence\ComposedMultiModelStore;
use EventEngine\Persistence\MultiModelStore;
use EventEngine\Persistence\TransactionalConnection;
use EventEngine\Prooph\V7\EventStore\ProophEventStore;
use EventEngine\Prooph\V7\EventStore\ProophEventStoreMessageFactory;
use EventEngine\Runtime\Flavour;
use EventEngine\Runtime\FunctionalFlavour;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventStore\EventStore as EventStoreProoph;
use Prooph\EventStore\Pdo\PersistenceStrategy;
use Prooph\EventStore\Pdo\PostgresEventStore;
use Prooph\EventStore\TransactionalActionEventEmitterEventStore;
use Psr\Log\LoggerInterface;

class EventEngineProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register() : void
    {
        $this->registerFlavour();
        $this->registerPersistency();
        $this->registerLogEngine();
        $this->registerEventEngine();
    }

    /**
     * Bootstrap services.
     */
    public function boot() : void
    {
    }

    private function registerFlavour() : void
    {
        $this->app->singleton(
            Flavour::class,
            static function () {
                return new FunctionalFlavour(
                    new ApplicationMessagePort(),
                    new ImmutableRecordDataConverter()
                );
            }
        );
    }

    private function registerPersistency() : void
    {
        $connection = DB::connection();

        $this->app->singleton(
            PersistenceStrategy::class,
            static function () {
                return new PostgresSingleStreamStrategy();
            }
        );

        $this->app->singleton(
            EventStoreProoph::class,
            static function (Application $application) use ($connection) {
                $eventStore = new PostgresEventStore(
                    new ProophEventStoreMessageFactory(),
                    $connection->getPdo(),
                    $application->make(PersistenceStrategy::class)
                );

                return new TransactionalActionEventEmitterEventStore(
                    $eventStore,
                    new ProophActionEventEmitter(TransactionalActionEventEmitterEventStore::ALL_EVENTS)
                );
            }
        );

        $this->app->singleton(
            TransactionalConnection::class,
            static function () use ($connection) {
                return new PostgresTransactionalConnection($connection->getPdo());
            }
        );

        $this->app->singleton(
            EventStore::class,
            static function (Application $app) {
                return new ProophEventStore($app->make(EventStoreProoph::class));
            }
        );

        $this->app->singleton(
            DocumentStore::class,
            static function () use ($connection) {
                return new PostgresDocumentStore(
                    $connection->getPdo(),
                    '', //No table prefix
                    'CHAR(36) NOT NULL', //Use alternative docId schema, to allow uuids as well as md5 hashes
                    false //Disable transaction handling, as this is controlled by the MultiModelStore
                );
            }
        );

        $this->app->singleton(
            MultiModelStore::class,
            static function (Application $app) {
                return new ComposedMultiModelStore(
                    $app->make(TransactionalConnection::class),
                    $app->make(EventStore::class),
                    $app->make(DocumentStore::class)
                );
            }
        );
    }

    private function registerLogEngine() : void
    {
        $this->app->singleton(
            LoggerInterface::class,
            static function () {
                return new Logger('EventEngine', [new StreamHandler('php://stderr')]);
            }
        );

        $this->app->bind(
            SimpleMessageEngine::class,
            static function (Application $app) {
                return new SimpleMessageEngine($app->make(LoggerInterface::class));
            }
        );
    }

    private function registerEventEngine() : void
    {
        $descriptions = [
            Command::class,
            Event::class,
            Aggregate::class,
            Query::class,
            Type::class,
            Listener::class,
        ];

        $this->app->singleton(
            EventEngine::class,
            static function (Application $app) use ($descriptions) {
                $eventEngine = new EventEngine(new OpisJsonSchema());

                foreach ($descriptions as $description) {
                    $eventEngine->load($description);
                }

                $eventEngine->disableAutoProjecting();

                $eventEngine
                    ->initialize(
                        $app->make(Flavour::class),
                        $app->make(MultiModelStore::class),
                        $app->make(SimpleMessageEngine::class),
                        $app
                    )
                    ->bootstrap((string) App::environment(), config('app.debug'));

                return $eventEngine;
            }
        );
    }
}
