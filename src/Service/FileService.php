<?php

namespace Vangel\Project\Service;

use Vangel\Project\Core\Resource\Service;
use Vangel\Project\Repository\FileRepository;
use Vangel\Project\Repository\TransactionRepository;

#[Service]
class FileService
{
    public function __construct(
        public TransactionParser     $parser,
        public TransactionRepository $transactionRepository,
        public FileRepository        $fileRepository
    )
    {
    }
}