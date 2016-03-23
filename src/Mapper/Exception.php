<?php

namespace Respect\Structural\Mapper;

use Ramsey\Uuid\UuidFactoryInterface;
use Respect\Structural\Mapper;

class Exception extends \Exception
{
    public static function disabledGenerateIdentifier()
    {
        $name = Mapper::ATTRIBUTTE_AUTO_GENERATE_ID;
        return new self("The id generator is disabled. Add a {$name} in options.");
    }

    public static function notValidIdGenerator($className)
    {
        return new self("The id generator is not and instance of " . UuidFactoryInterface::class . ", given a {$className}");
    }
}
