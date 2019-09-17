<?php

declare(strict_types=1);

namespace App\Domain\Model\Base;

interface AggregateCommand
{
    public function aggregateId() : string;
}
