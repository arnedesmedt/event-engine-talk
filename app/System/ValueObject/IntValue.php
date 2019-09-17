<?php

declare(strict_types=1);

namespace App\System\ValueObject;

abstract class IntValue extends ValueObject
{
    /** @var int */
    protected $value;

    protected function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return static
     */
    public static function fromInt(int $value)
    {
        return new static($value);
    }

    public function toInt() : int
    {
        return $this->value;
    }

    /**
     * @return static
     */
    public static function fromString(string $value)
    {
        return self::fromInt((int) $value);
    }

    public function __toString() : string
    {
        return '' . $this->toInt();
    }

    /**
     * @param mixed $other
     */
    public function isEqualTo($other) : bool
    {
        if (! $other instanceof self) {
            return false;
        }

        return $this->toInt() === $other->toInt();
    }
}
