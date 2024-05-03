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
        $acl_load = $acl->load();
        $this->view->allow_list = json_encode($acl->getAllowList());
        $this->view->roles = $acl->getRolesList($acl_load);
        $this->view->roles_desc = $acl->getRolesDesc($acl_load);
        $this->view->controllers = json_encode($acl->getControllersList());
        $this->view->controllers_desc = $acl->getControllersDesc();
        if ($this->request->isPost()) {
            if (isset($_POST['role']) && isset($_POST['role_desc'])) {
                $acl->initRole($_POST['role'], $_POST['role_desc']);
            } else if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                $accessData = json_decode(file_get_contents('php://input'), true);
                $acl->update($accessData);
            } else {
                $acl->removeRole($_POST);
            }
            $this->response->redirect('/access');
        }
    }
}
