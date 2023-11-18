<?php

namespace Vangel\Project\Repository;

use Vangel\Project\Core\Resource\Execute;
use Vangel\Project\Core\Resource\Query;
use Vangel\Project\Core\Resource\Repository;

#[Repository]
interface FileRepository
{

    #[Query("select file from _transaction;")]
    public function findFiles(): array;

    #[Execute]
    #[Query("delete from _transaction where file = :file;")]
    public function deleteFile(string $file): void;

}