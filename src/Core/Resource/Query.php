<?php


namespace Vangel\Project\Core\Resource;

use Attribute;
use ReflectionAttribute;
use ReflectionClass;

#[Attribute(Attribute::TARGET_METHOD)]
class Query
{
    public function __construct(public string $value, public bool $positional = false)
    {
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isPositional(): bool
    {
        return $this->positional;
    }
}
