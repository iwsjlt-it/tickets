<?php

declare(strict_types=1);
/**
 * Ошибки
 */
class ErrorsController extends Phalcon\Mvc\Controller
{

    public function code403Action()
    {
        $this->view->pageTitle = '403 Forbidden';
        
    }
    public function code401Action()
    {
        $this->view->pageTitle = '401 Unauthorized';
        
    }
    public function code404Action()
    {
        $this->view->pageTitle = '404 Not Found';

    }
}
