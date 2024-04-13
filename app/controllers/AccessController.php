<?php

declare(strict_types=1);

class AccessController extends SecurityController
{
    public function indexAction()
    {
        $this->view->pageTitle = 'Управление доступом';
        $acl = new AclService;
        $load = $acl->load();
        $this->view->acl = $load;
        if ($this->request->isPost()) {
            $acl->update($_POST);
            $this->response->redirect('/access');
        }
    }

    public function listAction()
    {
        $acl= new AclService();
        $func = $acl->getControllersAndActions();
    }
}
