<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount;

use App\Domain\Model\BankAccount\Query\GetBankAccounts as GetBankAccountsQuery;
use App\Domain\Resolver\Query;
use App\Domain\Resolver\Resolver;
use EventEngine\DocumentStore\DocumentStore;
use EventEngine\DocumentStore\Filter\AnyFilter;
use EventEngine\DocumentStore\Filter\LikeFilter;
use RuntimeException;

final class GetBankAccounts implements Resolver
{
    /** @var DocumentStore */
    private $documentStore;

    public function __construct(DocumentStore $documentStore)
    {
        $this->documentStore = $documentStore;
    }

    public function resolve(Query $query) : array
    {
        if (! $query instanceof GetBankAccountsQuery) {
            throw new RuntimeException('Query not supported');
        }

        $owner = $query->owner();
        $filter = $owner ? new LikeFilter('state.owner', $owner->toString()) : new AnyFilter();

        $bankAccounts = $this->documentStore->filterDocs('bank_accounts', $filter);

        return collect($bankAccounts)
            ->map(
                static function (array $bankAccount) {
                    return $bankAccount['state'];
                }
            )
            ->sortBy('accountId')
            ->values()
            ->toArray();
    }
}
