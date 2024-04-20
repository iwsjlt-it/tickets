<?php
use Phalcon\mvc\Controller;
/**
 * Пользователи
 */
class UsersController extends SecurityController
{

    public function indexAction()
    {
        $this->response->redirect('/users/list');
    }
    public function listAction()
    {
        $this->view->pageTitle = 'Учётные записи';
        $users = Users::find();
        $this->view->users = $users->toArray();
    }
    public function deleteAction()
    {
        $id = $this->request->get('id');
        $user = (new Users)->findFirstById($id);
        $user->delete();
        $this->response->redirect('/users');
    }
    public function updateAction($id)
    {
        $this->view->pageTitle = 'Изменение данных';
       // $id = $this->request->get('id');
        $user = (new Users)->findFirstById($id);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $user->assign($post);
            $success = $user->save();
            if ($success) {
                $this->response->redirect('/users');
            }
        }
        $this->view->user = $user->toArray();
    }
}
