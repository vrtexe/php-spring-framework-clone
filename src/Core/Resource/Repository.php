<?php


namespace Vangel\Project\Core\Resource;

use Attribute;
use ReflectionAttribute;
use ReflectionClass;

#[Attribute(Attribute::TARGET_CLASS)]
class Repository
{

    public static function isAnnotated(ReflectionClass $class): bool
    {
        $attributes = $class->getAttributes(static::class, ReflectionAttribute::IS_INSTANCEOF);
        return !empty($attributes);
    }
}
