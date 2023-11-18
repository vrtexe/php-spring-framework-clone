<?php

namespace Vangel\Project\Core;

use ReflectionClass;
use ReflectionException;
use Vangel\Project\Core\Resource\Component;
use Vangel\Project\Core\Resource\Controller;
use Vangel\Project\Core\Resource\Repository;
use Vangel\Project\Core\Resource\Service;

class ComponentLoader
{
    //This value should be the directory that contains composer.json
    const appRoot = __DIR__ . "/../../";

    const ignoredNamespaces = ["views"];
    const entrypoint = "index.php";

    /**
     * @param $namespace
     * @return ReflectionClass[][]
     * @throws ReflectionException
     */
    public static function loadComponents($namespace): array
    {
        /** @var ReflectionClass[] $controllers */
        $controllers = [];
        $components = [];
        $services = [];
        $repositories = [];
        $router = [new ReflectionClass(Router::class)];
        $classnames = self::getClassesInNamespace($namespace);

        foreach ($classnames as $classname) {
            $class = new ReflectionClass($classname);

            self::loadComponentInto($class, $components);
            self::loadServiceInto($class, $services);
            self::loadControllerInto($class, $controllers);
            self::loadRepositoriesInto($class, $repositories);
        }

        return [$router, $components, $repositories, $services, $controllers];
    }

    private static function loadControllerInto(ReflectionClass $class, array &$controllers): void
    {
        self::loadInto($class, $controllers, fn(...$args) => Controller::isAnnotated(...$args));
    }

    private static function loadRepositoriesInto(ReflectionClass $class, array &$repositories): void
    {
        self::loadInto($class, $repositories, fn(...$args) => Repository::isAnnotated(...$args));
    }

    private static function loadServiceInto(ReflectionClass $class, array &$components): void
    {
        self::loadInto($class, $components, fn(...$args) => Service::isAnnotated(...$args));
    }

    private static function loadComponentInto(ReflectionClass $class, array &$components): void
    {
        self::loadInto($class, $components, fn(...$args) => Component::isAnnotated(...$args));
    }

    /**
     * @param ReflectionClass $class
     * @param array $components
     * @param callable(ReflectionClass): bool $condition
     * @return void
     */
    private static function loadInto(ReflectionClass $class, array &$components, callable $condition): void
    {
        if ($condition($class)) {
            $components[] = $class;
        }
    }

    public static function getClassesInNamespace($namespace): array
    {
        $files = self::safeScanDir(self::getNamespaceDirectory($namespace));

        $classes = [];
        while ($files) {
            if (empty($files)) {
                break;
            }
            $file = array_pop($files);


            if (in_array($file, self::ignoredNamespaces)) {
                continue;
            }

            if (str_starts_with($file, self::entrypoint)) {
                continue;
            }

            if (str_ends_with($file, ".php")) {
                $classname = str_replace('.php', '', $file);
                $classes[] = "$namespace\\$classname";
            }

            $path = self::getNamespaceDirectory("$namespace\\$file");
            if (is_dir($path)) {
                $subDirectories = array_map(function ($path) use ($file) {
                    return "$file\\$path";
                }, self::safeScanDir($path));

                array_push($files, ...$subDirectories);
            }

        }

        return array_filter($classes, function ($possibleClass) {
            return class_exists($possibleClass) || interface_exists($possibleClass);
        });
    }

    private static function safeScanDir(string $directory): array
    {
        return array_filter(scandir($directory), function ($path) {
            return $path && $path != "." && $path !== "..";
        });

    }

    private static function getDefinedNamespaces(): array
    {
        $composerJsonPath = self::appRoot . 'composer.json';
        $composerConfig = json_decode(file_get_contents($composerJsonPath));

        return (array)$composerConfig->autoload->{'psr-4'};
    }

    private static function getNamespaceDirectory($namespace): false|string
    {
        $composerNamespaces = self::getDefinedNamespaces();

        $namespaceFragments = explode('\\', $namespace);
        $undefinedNamespaceFragments = [];

        while ($namespaceFragments) {
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if (array_key_exists($possibleNamespace, $composerNamespaces)) {
                return realpath(self::appRoot . $composerNamespaces[$possibleNamespace] . implode('/', $undefinedNamespaceFragments));
            }

            array_unshift($undefinedNamespaceFragments, array_pop($namespaceFragments));
        }

        return false;
    }
}

