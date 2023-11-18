<?php

namespace Vangel\Project\Controller;

use Vangel\Project\Core\Resource\Controller;
use Vangel\Project\Core\Route\GetRequest;
use Vangel\Project\Core\View;
use Vangel\Project\Model\TransactionsFileDto;
use Vangel\Project\Service\TransactionService;

#[Controller]
class TransactionController
{

    private static array $headers = ["Date", "Check #", "Description", "Amount"];

    public function __construct(public TransactionService $transactionService)
    {
    }

    #[GetRequest("/file")]
    public function previewFile(): View
    {
        $file = $_GET['q'];
        $transactions = $this->transactionService->findAllTransactions($file);
        [$totalIncome, $totalExpenses, $total] = $this->transactionService->performCalculations($transactions);

        return View::of("file")->with(new TransactionsFileDto(
            headers: static::$headers,
            file: $file,
            transactions: $transactions,
            totalIncome: $totalIncome,
            totalExpenses: $totalExpenses,
            total: $total
        ));
    }
}