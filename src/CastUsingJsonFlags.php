<?php

namespace JessArcher\CastableDataTransferObject;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CastUsingJsonFlags
{
    public function __construct(
        public int $encode = 0,
        public int $decode = 0,
    ) {}
}
