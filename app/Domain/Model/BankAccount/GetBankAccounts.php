<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount;

use EventEngine\DocumentStore\DocumentStore;
use EventEngine\DocumentStore\Filter\AnyFilter;
use EventEngine\DocumentStore\Filter\LikeFilter;
use EventEngine\Messaging\Message;
use EventEngine\Querying\Resolver;

final class GetBankAccounts implements Resolver
{
    /** @var DocumentStore */
    private $documentStore;

    public function __construct(DocumentStore $documentStore)
    {
        $this->documentStore = $documentStore;
    }

    public function resolve(Message $query) : array
    {
        $owner = $query->getOrDefault('owner', null);
        $filter = $owner ? new LikeFilter('state.owner', $owner) : new AnyFilter();

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
