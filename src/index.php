<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Vangel\Project\Core\Container;

const VIEW_PATH = __DIR__ . '/views';

session_start();

Container::start()->listen();
