<?php

declare(strict_types=1);

namespace App\System\ValueObject;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class UuidValue extends ValueObject
{
    /** @var UuidInterface */
    protected $value;

    private function __construct(UuidInterface $value)
    {
        $this->value = $value;
    }

    public static function fromUuid(UuidInterface $value) : self
    {
        return new static($value);
    }

    /**
     * @return static
     */
    public static function fromString(string $value) : self
    {
        return new static(Uuid::fromString($value));
    }

    public function toString() : string
    {
        return $this->value->toString();
    }

    public function __toString() : string
    {
        return $this->toString();
    }

    /**
     * @param mixed $other
     */
    public function isEqualTo($other) : bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->value->equals($other->value);
    }
}
