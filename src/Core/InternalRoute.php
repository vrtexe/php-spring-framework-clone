<?php

namespace Vangel\Project\Core;

use Closure;
use Vangel\Project\Core\HttpMethod;

class InternalRoute
{
  public function __construct(public HttpMethod $method, public string $path, public Closure $action)
  {
  }
}
