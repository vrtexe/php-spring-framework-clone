<?php

namespace Vangel\Project\Core;

use PDO;
use Vangel\Project\Core\Resource\Component;


/**
 * @mixin PDO
 */
#[Component]
class Datasource
{

    private PDO $database;

    public function __construct()
    {
        $dir = __DIR__;
        $this->database = new PDO("sqlite:$dir/../file.db", options: [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
    }

    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->database, $name], $arguments);
    }

}