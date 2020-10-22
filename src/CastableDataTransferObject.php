<?php

namespace JessArcher\CastableDataTransferObject;

use Illuminate\Contracts\Database\Eloquent\Castable;
use JessArcher\CastableDataTransferObject\Casts\DataTransferObject as DataTransferObjectCast;
use Spatie\DataTransferObject\DataTransferObject;
use function Safe\json_decode;
use function Safe\json_encode;

abstract class CastableDataTransferObject extends DataTransferObject implements Castable
{
    public static function castUsing(array $attributes)
    {
        return new DataTransferObjectCast(static::class);
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public static function fromJson($json)
    {
        return new static(json_decode($json, true));
    }
}
