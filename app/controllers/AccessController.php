<?php

declare(strict_types=1);
/**
 * Доступ
 */
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
        $this->view->pageTitle = 'Управление доступом';
        $acl = new AclService;
        $acl_load = $acl->load();
        $this->view->allow_list = json_encode($acl->getAllowList());
        $this->view->roles = $acl->getRolesList($acl_load);
        $this->view->roles_desc = $acl->getRolesDesc($acl_load);
        $this->view->controllers = json_encode($acl->getControllersList());
        $this->view->controllers_desc = $acl->getControllersDesc();
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $accessData = '';
            foreach ($post as $data) {
                $accessData = $data;
            }
            $accessData = json_decode($accessData, true);
            $acl->update($accessData);
        }
    }
    public function jslistAction()
    {
        $this->view->pageTitle = 'Управление доступом';
        $acl = new AclService;
        $acl_load = $acl->load();
        $this->view->allow_list = json_encode($acl->getAllowList());
        $this->view->roles = $acl->getRolesList($acl_load);
        $this->view->roles_desc = $acl->getRolesDesc($acl_load);
        $this->view->controllers = json_encode($acl->getControllersList());
        $this->view->controllers_desc = $acl->getControllersDesc();
        if ($this->request->isPost()) {
            //
        }
    }
}
