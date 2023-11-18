<?php


namespace Vangel\Project\Core\Resource;

use Attribute;
use ReflectionAttribute;
use ReflectionClass;

#[Attribute(Attribute::TARGET_CLASS)]
class Component
{


    public static function isAnnotated(ReflectionClass $class): bool
    {
        $attributes = $class->getAttributes(static::class, ReflectionAttribute::IS_INSTANCEOF);
        return !empty($attributes);
    }

    public static function load(ReflectionClass $class): ?ReflectionClass
    {
        $attributes = $class->getAttributes(static::class, ReflectionAttribute::IS_INSTANCEOF);

        if (empty($attributes)) {
            return null;
        }

        $attribute = $attributes[0]?->newInstance();

        if (!$attribute) {
            return null;
        }

        return $class;
    }
}
