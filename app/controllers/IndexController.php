<?php

declare(strict_types=1);
/**
 * Главная страница
 */
class IndexController extends SecurityController
{
    public function indexAction()
    {
        $this->view->pageTitle = 'Главная';
    }
}
