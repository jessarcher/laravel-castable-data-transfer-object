<?php

namespace JessArcher\CastableDataTransferObject\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

class DataTransferObject implements CastsAttributes
{
    public function __construct(
        /** @var string The DataTransferObject class to cast to */
        protected string $class,
    ) {}

    /**
     * Cast the stored value to the configured DataTransferObject.
     */
    public function get($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return;
        }

        return $this->class::fromJson($value);
    }

    /**
     * Prepare the given value for storage.
     */
    public function set($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return;
        }

        if (is_array($value)) {
            $value = new $this->class($value);
        }

        if (! $value instanceof $this->class) {
            throw new InvalidArgumentException("Value must be of type [$this->class], array, or null");
        }

        return $value->toJson();
    }
}
