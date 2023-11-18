<?php

namespace Vangel\Project\Repository;

use Vangel\Project\Core\Resource\Execute;
use Vangel\Project\Core\Resource\Query;
use Vangel\Project\Core\Resource\Repository;
use Vangel\Project\Model\Transaction;

#[Repository]
interface TransactionRepository
{

    #[Query("select * from _transaction where file = :file;")]
    public function findAll(string $file): array;


    #[Execute]
    #[Query("
        insert into _transaction (file, date, check_id, description, amount)
        values (:file, :date, :checkId, :description, :amount);
    ")]
    public function insert(string $file, string $date, string $checkId, string $description, float $amount): void;

}