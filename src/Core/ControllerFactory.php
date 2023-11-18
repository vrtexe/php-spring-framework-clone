<?php

namespace Vangel\Project\Core;

use ReflectionException;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

class ControllerFactory
{

    /**
     * @param object $object
     * @return InternalRoute[]
     * @throws ReflectionException
     */
    public static function register(object $object): array
    {

        $reflectionClass = new ReflectionClass($object::class);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $attributes = $method->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF);
            if (!$attributes) {
                continue;
            }

            /** @var Route $attribute */
            $attribute = $attributes[0]?->newInstance();

            $routes[] = new InternalRoute(
                $attribute->method,
                $attribute->path,
                $method->getClosure($object)
            );
        }

        return $routes ?? [];
    }
}
