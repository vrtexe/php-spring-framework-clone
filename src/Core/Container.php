<?php

namespace Vangel\Project\Core;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use Vangel\Project\Core\Resource\Controller;
use Vangel\Project\Core\Resource\Repository;

class Container
{
    public array $components = [];
    private RepositoryBuilder $repositoryBuilder;

    public function __construct()
    {
        $this->repositoryBuilder = new RepositoryBuilder();
    }


    /**
     * @throws ReflectionException
     */
    public static function start(): self
    {
        $container = new Container();
        $container->run();
        return $container;
    }


    public function listen(): void
    {
        /** @var $router Router */
        $router = $this->get(Router::class);
        echo $router->resolve();
    }

    public function add(object $component, ?ReflectionClass $override = null): void
    {
        if ($override) {
            $this->components[$override->getName()] = $component;
        } else {
            $this->components[$component::class] = $component;
            $this->addInterfaceImplementations($component);
        }
    }

    public function get(string $class)
    {
        return $this->components[$class];
    }

    /**
     * @throws ReflectionException
     */
    public function run(): void
    {
        [[$router], $components, $repositories, $services, $controllers] = ComponentLoader::loadComponents("Vangel\Project");

        /** @var Router $routerInstance */
        $routerInstance = $router->newInstance();
        $this->add($routerInstance);

        $this->injectDependencies([...$components, ...$services, ...$controllers], $repositories);
        $this->injectRouterDependencies($routerInstance);
    }

    private function injectRouterDependencies(Router &$router)
    {
        foreach ($this->components as $component) {
            $class = new ReflectionClass($component::class);
            if (Controller::isAnnotated($class)) {
                $router->registerController($component);
            }
        }
    }

    /**
     * @param ReflectionClass[] $classes
     * @return void
     * @throws Exception
     */
    private function injectDependencies(array $classes, array $repositories): void
    {

        $repositoryBase = new ReflectionClass(AbstractRepository::class);
        $repositoriesDependencies = array_map(fn($class) => [$class, $this->loadCriteria($repositoryBase)], $repositories);
        $dependencies = [...array_map(fn($class) => [$class, $this->loadCriteria($class)], $classes), ...$repositoriesDependencies];

        $retries = [];

        while ($dependencies) {
            $component = array_pop($dependencies);
            /** @var ReflectionClass $class */
            [$class, $criteria] = $component;
            $className = $class->getName();

            if (array_key_exists($className, $retries) && $retries[$className] > 10) {
                throw new \Exception("Circular or missing dependency for class: $className");
            }

            if (!$criteria()) {
                $retries[$className] = ($retries[$className] ?? 0) + 1;
                array_unshift($dependencies, $component);
                continue;
            }

            unset($retries[$className]);

            $override = null;

            if (Repository::isAnnotated($class)) {

                $repositoryImplementationClass = ClassGenerator::createDynamicClass(AbstractRepository::class, $class->getName());
                $repositoryImplementation = new ReflectionClass($repositoryImplementationClass);

                $arguments = $this->buildComponentArgs($repositoryImplementation);
                $instance = $repositoryImplementation->newInstanceArgs($arguments);
                $this->repositoryBuilder->build($class, $instance);
                $override = $class;
            } else if ($class->getConstructor()) {
                $arguments = $this->buildComponentArgs($class);
                $instance = $class->newInstanceArgs($arguments);
            } else {
                $instance = $class->newInstanceWithoutConstructor();
            }

            $this->add($instance, $override);
        }
    }


    private function buildComponentArgs(ReflectionClass $class): array
    {
        $parameters = $class->getConstructor()?->getParameters() ?? [];
        $arguments = [];
        foreach ($parameters as $key => $parameter) {
            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType) {
                $arg = $this->components[$type->getName()];

                if (is_array($arg)) {
                    $implementations = count($arg);
                    if ($implementations < 1) {
                        throw new Exception("No implementation found for: {$type->getName()}");
                    }

                    if ($implementations > 1) {
                        throw new Exception("Multiple implementations found for: {$type->getName()}");
                    }

                    $arg = $arg[-1];
                }

                if (!$arg) {
                    throw new Exception("Component of type: {$type->getName()} not initialized");
                }

                $arguments[$key] = (object) $arg;

            }
        }

        return $arguments;
    }

    private function loadCriteria(ReflectionClass $class): callable
    {
        $types = array_map(fn($param) => $param->getType()->getName(), $class->getConstructor()?->getParameters() ?? []);
        $components = &$this->components;

        return function () use ($types, &$components, $class) {
            foreach ($types as $type) {
                if (!array_key_exists($type, $components)) {
                    return false;
                }
            }

            return true;
        };
    }


    /**
     * @throws ReflectionException
     */
    private function addInterfaceImplementations(object $component): void
    {
        $class = new ReflectionClass($component::class);

        foreach ($class->getInterfaceNames() as $interface) {
            $this->components[$interface] = [
                ...($this->components[$interface] ?? []),
                $component
            ];
        }

        if ($class->getParentClass() && $class->getParentClass()?->isAbstract()) {
            $parentClassName = $class->getParentClass()->getName();
            $this->components[$parentClassName] = [
                ...($this->components[$parentClassName] ?? []),
                $component
            ];
        }
    }


}