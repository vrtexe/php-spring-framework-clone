<?php

namespace Vangel\Project\Core;

enum HttpMethod: string
{
  case GET = "GET";
  case POST = "POST";
  case PUT = "PUT";
  case PATCH = "PATCH";
  case OPTIONS = "OPTIONS";
  case DELETE = 'DELETE';
}
