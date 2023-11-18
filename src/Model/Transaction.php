<?php

namespace Vangel\Project\Model;

use DateTime;
use Vangel\Project\Service\Common;

readonly class Transaction
{


  public function __construct(
    public DateTime $date,
    public ?string $checkId,
    public string $description,
    public float $amount,
  ) {
  }

  function formattedDate(): string
  {
    return $this->date->format("Y-m-d");
  }

  function formattedAmount(): string
  {
      return Common::formatDollars($this->amount);
  }

}
