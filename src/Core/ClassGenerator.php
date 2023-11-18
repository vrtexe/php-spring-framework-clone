<?php

namespace Vangel\Project\Core;

use ReflectionClass;
use ReflectionMethod;

class ClassGenerator
{


    public static function createDynamicClass(string $actorClass, string $implements): string
    {
        $class = PhpClassname::FQCN($actorClass);
        $interface = PhpClassname::FQCN($implements);

        $actorClassname = static::generateActorClassname($class, $interface);

        if (!class_exists($actorClassname)) {
            eval(static::generateActorClass($actorClassname, $class, $interface));
        }

        return $actorClassname;
    }

    private static function generateActorClass($actorClassname, $classFQCN, $interfaceFQCN): string
    {

        $newActorClass = new PhpClassname($actorClassname);
        $actorNamespace = $newActorClass->getNamespace();
        $actorBaseClassname = $newActorClass->getBasename();

        $interfaceMethodsImplementations = static::generateInterfaceMethods($interfaceFQCN);

        return "
            namespace $actorNamespace;

            #[\AllowDynamicProperties]
            class $actorBaseClassname extends $classFQCN implements $interfaceFQCN {
             
                $interfaceMethodsImplementations
            }
        ";
    }

    private static function generateInterfaceMethods(string $interface): string
    {
        $interfaceReflection = new ReflectionClass($interface);
        $methods = array_map(fn($m) => self::generateMethod($m), $interfaceReflection->getMethods());
        return join("\n", $methods);
    }

    private static function generateMethod(ReflectionMethod $method): string
    {
        $methodName = $method->getName();
        $returnType = $method->getReturnType()->getName();

        $isVoid = $returnType === "void";
        $prefix = !$isVoid ? "return" : "";

        return "
            public function $methodName(...\$args): $returnType {
                $prefix \$this->__call('$methodName', \$args);
            }
        ";
    }


    private static function generateActorClassname(string $classFQCN, string $interfaceFQCN): string
    {

        $interfaceHash = PhpClassname::hash($interfaceFQCN);

        return sprintf("%s▶%s◧", $classFQCN, $interfaceHash);
    }

}