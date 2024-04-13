<?php

declare(strict_types=1);

class ErrorsController extends Phalcon\Mvc\Controller
{

    public function code403Action()
    {
        $this->view->pageTitle = '403 Forbidden';
        header("refresh:3;url=/");
    }
}
