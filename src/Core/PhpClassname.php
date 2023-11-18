<?php

namespace Vangel\Project\Core;

class PhpClassname
{
    const SEPARATOR = '\\';

    static function FQCN($classname): string
    {
        return self::SEPARATOR . ltrim($classname, self::SEPARATOR);
    }

    static function hash($classname): string
    {
        $string = ltrim($classname, self::SEPARATOR);
        return strtr($string, [self::SEPARATOR => "╲"]);
    }

    protected string $name;

    function __construct($string)
    {

        $this->name = (string)$string;
    }

    function __toString()
    {

        return $this->name;
    }

    function getNamespace(): string
    {
        return ltrim($this->extractNamespace($this->name), self::SEPARATOR);
    }

    function getBasename(): string
    {
        $classname = $this->name;

        $pos = strlen($this->extractNamespace($classname));

        return ltrim(substr($classname, $pos), self::SEPARATOR);
    }


    private function extractNamespace($classname)
    {

        // TODO: Check for PHP string function: locate last occurrence of a string and return everything before it
        $pos = strrpos($classname, self::SEPARATOR);

        if ($pos === false) {
            return $classname;
        }

        return rtrim(substr($classname, 0, $pos), self::SEPARATOR);
    }

}