<?php

namespace Vangel\Project\Model;

enum TransactionColumn: int
{
  case Date = 0;
  case CheckId = 1;
  case Description = 2;
  case Amount = 3;
}
