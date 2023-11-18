<?php

namespace Vangel\Project\Core;

use AllowDynamicProperties;
use Closure;
use PDO;
use PDOStatement;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Vangel\Project\Core\Resource\Execute;
use Vangel\Project\Core\Resource\Query;

class RepositoryBuilder
{

    public function __construct()
    {
    }

    function build(ReflectionClass $class, DatasourceProvider &$instance): void
    {
        foreach ($class->getMethods() as $method) {
            $attributes = $method->getAttributes(Query::class, ReflectionAttribute::IS_INSTANCEOF);
            if (!$attributes) {
                continue;
            }

            $instance->{$method->getName()} = $this->buildMethod($method, $instance);
        }
    }

    private function buildMethod(ReflectionMethod $method, DatasourceProvider $instance): Closure
    {
        $queryAttribute = $this->extractQueryAttribute($method);
        $isExecutable = $this->extractExecuteAttribute($method);

        return $this->buildQueryMethod($method, $instance, $queryAttribute, $isExecutable);
    }

    private function buildQueryMethod(ReflectionMethod $method, DatasourceProvider $instance, Query $queryAttribute, ?Execute $executeAttribute): Closure
    {
        $datasource = $instance->getDatasource();
        $query = $queryAttribute->getValue();
        $isPositional = $queryAttribute->isPositional();

        $buildArguments = $this->createArgumentBuilder($method, $isPositional);
        $executeStatement = $this->createStatementExecutor($executeAttribute);

        return function (...$args) use ($datasource, $query, $buildArguments, $executeStatement) {
            $datasource->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $statement = $datasource->prepare($query);
            $arguments = $buildArguments($args);
            return $executeStatement($arguments, $statement);
        };
    }

    private function createStatementExecutor(?Execute $execute): Closure
    {
        if ($execute) {
            return fn(array $args, PDOStatement $statement) => $statement->execute($args);
        }

        return fn(array $args, PDOStatement $statement) => $statement->execute($args) ? $statement->fetchAll() : [];
    }

    /**
     * @param ReflectionMethod $method
     * @param bool $isPositional
     * @return Closure(array $args): array
     */
    private function createArgumentBuilder(ReflectionMethod $method, bool $isPositional): Closure
    {
        if ($isPositional) {
            return fn(array $args) => $args;
        }

        $params = array_map(fn($param) => ":" . $param->getName(), $method->getParameters());
        return fn(array $args) => array_combine($params, $args);
    }

    private function extractQueryAttribute(ReflectionMethod $method): ?Query
    {
        return $this->extractAttribute($method, Query::class);
    }

    private function extractExecuteAttribute(ReflectionMethod $method): ?Execute
    {
        return $this->extractAttribute($method, Execute::class);
    }

    private function extractAttribute(ReflectionMethod $method, string $class): ?object
    {
        $attributes = $method->getAttributes($class, ReflectionAttribute::IS_INSTANCEOF);
        if (!$attributes) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

}