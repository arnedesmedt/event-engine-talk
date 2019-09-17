<?php

declare(strict_types=1);

namespace App\Domain\Resolver;

interface Resolver
{
    /**
     * @return mixed
     */
    public function resolve(Query $query);
}
