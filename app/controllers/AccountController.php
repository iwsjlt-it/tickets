<?php

declare(strict_types=1);
class AccountController extends SecurityController
{
    public function indexAction()
    {
        $this->view->pageTitle = 'Аккаунт';
        if ($this->session->has('userId')) {
            
            $user = (new Users())->findFirstById($this->session->get('userId'));
            echo $user->name . PHP_EOL;
            var_dump($this->session->get('userId'));
            var_dump($user->role);
            
           
        } else {
            echo '[Как вы сюда попали?]';
        }
    }
    public function loginAction()
    {
        $this->view->pageTitle = 'Вход в систему';
        $validation = new LoginValidation();

        if ($this->request->isPost()) {
            // Запись, фильтрация и валидация данных
            $post = $this->request->getPost();
            $post = $this->filter->sanitize($post, ['trim', 'striptags', 'string']);
            $post['email'] = $this->filter->sanitize($post['email'], ['email', 'trim', 'striptags', 'string']);
            $messages = $validation->validate($post);

            // Проверка успешности валидации
            if (count($messages) === 0) {
                // Поиск пользователя по логину
                $user = (new Users())->findFirstByemail($post['email']);
                if ($user) {
                    if ($this->security->checkHash($post['password'], $user->password)) {
                        $this->session->set('userId', $user->id);
                        //$this->session->set('userRole', $user->role);
                        $this->response->redirect('/');
                    } else {
                        $content = 'Неверная почта или пароль';
                        $this->view->content = $this->response->setContent($content);
                    }
                } else {
                    $content = 'Неверная почта или пароль';
                    $this->view->content = $this->response->setContent($content);
                }
            } else {
                foreach ($messages as $message) {
                    $errors[$message->getField()] = $message->getMessage();
                }
                $this->view->errors = $errors;
            }
        }
        $this->view->content = $this->response->getContent();
    }

    public function signupAction()
    {
        $this->view->pageTitle = 'Регистрация';
        $validation = new SignupValidation();

        if ($this->request->isPost()) {
            // Запись, фильтрация и валидация данных
            $post = $this->request->getPost();
            $post = $this->filter->sanitize($post, ['trim', 'striptags', 'string']);
            $post['email'] = $this->filter->sanitize($post['email'], ['email', 'trim', 'striptags', 'string']);
            $messages = $validation->validate($post);

            if (count($messages) === 0) {
                // Создание шаблона модели и запись данных
                $user = new Users($post);
                $user->name = $post['last_name'] . ' ' . $post['first_name'];
                $user->password = $this->security->hash($post['password']);
                $user->role = 'client';
                $user->status = 'unactive';

                // Запись в базу данных
                $success = $user->save();

                // Проверка успешности валидации

                if ($success) {
                    $this->response->redirect('/account/login');
                }
            } else {
                foreach ($messages as $message) {
                    $errors[$message->getField()] = $message->getMessage();
                }
                $this->view->errors = $errors;
            }
        }
    }
    public function recoveryAction()
    {
        $this->view->pageTitle = 'Восстановление доступа';
    }
    public function logoutAction()
    {
        $this->view->pageTitle = 'Выйти';
        $this->response->redirect('/account/login');
        $this->session->destroy();
    }
}
