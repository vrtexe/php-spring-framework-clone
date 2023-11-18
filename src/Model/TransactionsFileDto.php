<?php

namespace Vangel\Project\Model;

use Vangel\Project\Service\Common;

final class TransactionsFileDto
{
    /**
     * @param string[] $headers
     * @param Transaction[] $transactions
     * @param float $totalIncome
     * @param float $totalExpenses
     * @param float $total
     */
    public function __construct(
        public array $headers,
        public string $file,
        public array $transactions,
        public float $totalIncome,
        public float $totalExpenses,
        public float $total)
    {
    }


    function getTotalIncomeFormatted(): string
    {
        return Common::formatDollars($this->totalIncome);
    }

    function getTotalExpensesFormatted(): string
    {
        return Common::formatDollars($this->totalExpenses);
    }

    function getTotalFormatted(): string
    {
        return Common::formatDollars($this->total);
    }
}