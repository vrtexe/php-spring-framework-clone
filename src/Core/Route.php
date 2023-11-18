<?php

namespace Vangel\Project\Core;

use Attribute;

#[Attribute]
class Route
{
  public function __construct(public HttpMethod $method, public string $path)
  {
  }
}
