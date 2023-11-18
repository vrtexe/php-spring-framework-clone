<?php

namespace Vangel\Project\Core;

use AllowDynamicProperties;
use Vangel\Project\Core\Resource\PostConstruct;
use Vangel\Project\Core\Resource\Query;
use Vangel\Project\Model\Transaction;
use Vangel\Project\Repository\TransactionRepository;

#[AllowDynamicProperties]
class AbstractRepository implements DatasourceProvider
{

    public function __construct(public Datasource $datasource)
    {
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array($this->{$name}, $arguments);
    }

    public function __set(string $name, $value): void
    {
        $this->{$name} = $value;
    }

    function getDatasource(): Datasource
    {
        return $this->datasource;
    }
}