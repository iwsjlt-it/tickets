<?php

declare(strict_types=1);
class IndexController extends SecurityController
{
    public function indexAction()
    {
        $this->view->pageTitle = 'Главная';
    }
}
