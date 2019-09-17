<?php

declare(strict_types=1);

namespace App\System\ValueObject;

abstract class ValueObject
{
    abstract public function __toString() : string;

    /**
     * @param mixed $other
     */
    abstract public function isEqualTo($other) : bool;
}
