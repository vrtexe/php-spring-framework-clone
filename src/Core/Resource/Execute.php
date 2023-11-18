<?php


namespace Vangel\Project\Core\Resource;

use Attribute;
use ReflectionAttribute;
use ReflectionClass;

#[Attribute(Attribute::TARGET_METHOD)]
class Execute
{
    public function __construct()
    {
    }
}
