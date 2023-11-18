<?php

namespace Vangel\Project\Service;

use DateTime;
use stdClass;
use Vangel\Project\Core\Resource\Service;
use Vangel\Project\Model\Transaction;
use Vangel\Project\Repository\FileRepository;
use Vangel\Project\Repository\TransactionRepository;

#[Service]
class TransactionService
{

    public function __construct(
        public TransactionParser     $parser,
        public TransactionRepository $transactionRepository,
        public FileRepository        $fileRepository
    )
    {
    }

    /**
     * @return Transaction[]
     */
    public function findAllTransactions(string $file): array
    {
        return array_map(fn($t) => $this->mapToTransaction($t), $this->transactionRepository->findAll($file));
    }

    public function findFileNames(): array
    {
        return array_unique(
            array_map(fn($r) => $r->file, $this->fileRepository->findFiles())
        );
    }

    public function saveFileData(string $fileName, string $fileLocation)
    {
        $transactions = $this->parser->parseFile($fileLocation);

        foreach ($transactions as $transaction) {
            $this->transactionRepository->insert(
                $fileName,
                $transaction->date->format("Y-m-d"),
                $transaction->checkId,
                $transaction->description,
                $transaction->amount
            );
        }
    }

    public function deleteFile(string $file)
    {
        $this->fileRepository->deleteFile($file);
    }

    private function mapToTransaction(stdClass $transaction): Transaction
    {
        return new Transaction(
            new DateTime($transaction->date),
            $transaction->check_id,
            $transaction->description,
            $transaction->amount,
        );
    }


    /**
     * @param Transaction[] $transactions
     */
    public function performCalculations(array $transactions)
    {
        $totalIncome = 0.0;
        $totalExpenses = 0.0;
        foreach ($transactions as $transaction) {
            if ($transaction->amount > 0) {
                $totalIncome += $transaction->amount;
            } else {
                $totalExpenses += $transaction->amount;
            }
        }

        return [$totalIncome, $totalExpenses, $totalIncome + $totalExpenses];
    }
}
