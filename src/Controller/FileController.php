<?php

namespace Vangel\Project\Controller;

use Vangel\Project\Core\Resource\Controller;
use Vangel\Project\Core\Route\GetRequest;
use Vangel\Project\Core\Route\PostRequest;
use Vangel\Project\Core\View;
use Vangel\Project\Model\FilesDto;
use Vangel\Project\Service\TransactionService;

#[Controller]
class FileController
{

    public function __construct(public TransactionService $transactionService)
    {
    }


    #[GetRequest('/index')]
    public function index(): View
    {
        return View::of("index")->with(new FilesDto($this->transactionService->findFileNames()));
    }

    #[GetRequest("/delete")]
    public function uploadFile(): View
    {
        $file = $_GET['q'];

        $this->transactionService->deleteFile($file);

        return View::of("index")->with(new FilesDto($this->transactionService->findFileNames()));
    }
}
