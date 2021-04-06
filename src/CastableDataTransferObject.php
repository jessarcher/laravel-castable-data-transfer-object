<?php

namespace JessArcher\CastableDataTransferObject;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JessArcher\CastableDataTransferObject\Casts\DataTransferObject as DataTransferObjectCast;
use Spatie\DataTransferObject\DataTransferObject;
use function Safe\json_decode;
use function Safe\json_encode;

abstract class CastableDataTransferObject extends DataTransferObject implements Castable, Arrayable, Jsonable
{
    public static function castUsing(array $arguments)
    {
        return new DataTransferObjectCast(static::class);
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    public static function fromJson(string $json, int $options = 0)
    {
        return new static(json_decode($json, assoc: true, options: $options));
    }
}
