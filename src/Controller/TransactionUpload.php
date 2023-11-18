<?php

namespace Vangel\Project\Controller;

use Vangel\Project\Core\Resource\Controller;
use Vangel\Project\Core\Route\GetRequest;
use Vangel\Project\Core\Route\PostRequest;
use Vangel\Project\Core\View;
use Vangel\Project\Model\FilesDto;
use Vangel\Project\Model\Transaction;
use Vangel\Project\Model\TransactionsFileDto;
use Vangel\Project\Service\TransactionService;

#[Controller]
class TransactionUpload
{



    public function __construct(public TransactionService $transactionService)
    {
    }

    #[GetRequest("/upload")]
    public function previewFile(): View
    {
        return View::of("upload");
    }

    #[PostRequest("/upload")]
    public function uploadFile(): View
    {
        $fileLocation = $_FILES['data']['tmp_name'];
        $fileName = $_FILES['data']['name'];

        $this->transactionService->saveFileData($fileName,  $fileLocation);

        return View::of("index")->with(new FilesDto($this->transactionService->findFileNames()));
    }

}