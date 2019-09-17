<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount;

use App\Domain\Model\BankAccount\Query\GetBankAccount as GetBankAccountQuery;
use App\Domain\Resolver\Query;
use App\Domain\Resolver\Resolver;
use EventEngine\DocumentStore\DocumentStore;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetBankAccount implements Resolver
{
    /** @var DocumentStore */
    private $documentStore;

    public function __construct(DocumentStore $documentStore)
    {
        $this->documentStore = $documentStore;
    }

    public function resolve(Query $query) : array
    {
        if (! $query instanceof GetBankAccountQuery) {
            throw new RuntimeException('Query not supported');
        }

        $bankAccount = $this->documentStore->getDoc('bank_accounts', $query->accountId()->toString());

        if (! $bankAccount) {
            throw new NotFoundHttpException(
                'Bank account not found'
            );
        }

        return $bankAccount['state'];
    }
}
