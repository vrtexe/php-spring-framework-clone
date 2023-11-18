<?php

declare(strict_types=1);

namespace Vangel\Project\Service;

use DateTime;
use Vangel\Project\Core\Resource\Component;
use Vangel\Project\Model\Transaction;
use Vangel\Project\Model\TransactionColumn;

#[Component]
class TransactionParser
{


  /**
   * @return Transaction[]
   */
  function parseFile(string $path): array
  {
    $file = fopen($path, 'r');
    $lines = $this->getLines($file);

    foreach ($lines as $index => $line) {
      if ($index === 0) {
        continue;
      }

      $transactions[] = $this->parseLine($line);
    }

    fclose($file);
    return $transactions ?? [];
  }


  /**
   * @param resource $file
   * 
   * @return \Generator<string>
   */
  function getLines(mixed $file): \Generator
  {
    while (!feof($file)) {
      yield fgetcsv($file);
    }
  }

  /**
   * @param string[] $line
   */
  function parseLine(array $line)
  {
    $columns = $line;

    return new Transaction(
      date: $this->parseDate(...$columns),
      checkId: $columns[TransactionColumn::CheckId->value],
      description: $columns[TransactionColumn::Description->value],
      amount: Common::parseDollars($columns[TransactionColumn::Amount->value])
    );
  }

  function parseDate(string ...$columns): \DateTime
  {
    return DateTime::createFromFormat("m/d/Y", $columns[TransactionColumn::Date->value]);
  }


  /**
   * @return string[]
   */
  function csvExplode(string $line): array
  {

    $currentColumnStart = 0;
    $currentColumnEndExclude = 0;
    $lineLength = strlen($line);
    $columnIsOpened = false;
    foreach (str_split($line) as $index => $character) {
      if ($character === '"') {
        !$columnIsOpened && $currentColumnStart = $index + 1;
        !$columnIsOpened && $currentColumnEndExclude = 1;
        $columnIsOpened = !$columnIsOpened;
      }

      if (!$columnIsOpened && ($character === "," || $index === $lineLength - 1)) {
        $columns[] = substr($line, $currentColumnStart, $index - $currentColumnStart - $currentColumnEndExclude);
        $currentColumnStart = $index + 1;
        $currentColumnEndExclude = 0;
      }
    }

    return $columns ?? [];
  }
}
