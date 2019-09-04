<?php

declare(strict_types=1);

namespace App\Domain\Model\BankAccount;

use EventEngine\DocumentStore\DocumentStore;
use EventEngine\Messaging\Message;
use EventEngine\Querying\Resolver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetBankAccount implements Resolver
{
    /** @var DocumentStore */
    private $documentStore;

    public function __construct(DocumentStore $documentStore)
    {
        $this->documentStore = $documentStore;
    }

    public function resolve(Message $query) : array
    {
        $bankAccount = $this->documentStore->getDoc('bank_accounts', $query->get('accountId'));

        if (! $bankAccount) {
            throw new NotFoundHttpException(
                'Bank account not found'
            );
        }

        return $bankAccount['state'];
    }
}
