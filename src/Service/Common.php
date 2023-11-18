<?php

namespace Vangel\Project\Service;

class Common
{
  public static function formatDollars(float $value): string
  {
    $absoluteValue = abs($value);
    return ($absoluteValue === $value ? '' : '-') .  '$' . number_format($absoluteValue, 2);
  }

    public static function parseDollars(string $value): float
    {
        $amountValue =  str_replace("$", "",  $value);
        return floatval($amountValue) ?? -1;
    }
}
