<?php

namespace Vangel\Project\Core\Route;

use Attribute;
use Vangel\Project\Core\HttpMethod;
use Vangel\Project\Core\Route;

#[Attribute]
class OptionsRequest extends Route
{
  public function __construct(string $path)
  {
    parent::__construct(HttpMethod::OPTIONS, $path);
  }
}
