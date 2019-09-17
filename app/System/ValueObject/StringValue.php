<?php

declare(strict_types=1);

namespace App\System\ValueObject;

abstract class StringValue extends ValueObject
{
    /** @var string */
    protected $value;

    protected function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return static
     */
    public static function fromString(string $value)
    {
        return new static($value);
    }

    public function toString() : string
    {
        return $this->value;
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

        return $this->toString() === $other->toString();
    }
}
