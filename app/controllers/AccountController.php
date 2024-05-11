<?php

declare(strict_types=1);
/**
 * Аккаунт
 */
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
                        $this->view->content = 'Неверная почта или пароль';
                    }
                } else {
                    $this->view->content = 'Неверная почта или пароль';
                }
            } else {
                foreach ($messages as $message) {
                    $errors[$message->getField()] = $message->getMessage();
                }
                $this->view->errors = $errors;
            }
        }
        //$this->view->content = $this->response->getContent();
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
    public function resetAction()
    {
        $this->view->pageTitle = 'Сброс пароля';
        if ($this->request->isPost()) {
            if ($user = (Users::findFirstByEmail($this->request->getPost('email', ['email', 'trim', 'striptags', 'string'])))) {

                $uuid = $this->security->getRandom()->uuid();
                $reset_password_record = new ResetPassword();
                if ($reset_password_record_exists = ResetPassword::findFirstByEmail($user->email)) {
                    $reset_password_record_exists->delete("email={$user->email}");
                }
                $reset_password_record->email = $user->email;
                $reset_password_record->uuid = $uuid;

                if (!$reset_password_record->save()) {
                    echo 'Ошибка';
                    return false;
                }

                $this->mail->setFrom('zed-n.v@mail.ru', 'ООО "Зуммер"');
                $this->mail->addAddress($user->email, $user->name);
                $this->mail->Subject = 'Сброс пароля';
                //Процентное кодирование для корректного URL
                $percentage_coding = str_replace('@', '%40', $user->email);

                //Временная уникальная ссылка
                $link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' .  $_SERVER['HTTP_HOST'] . "/account/newPassword/{$uuid}?email={$percentage_coding}";

                $mail_content = file_get_contents(APP_PATH . '/services/mail/recoveryMail.html');
                $mail_content = str_replace('<p class="name">Здравствуйте, .</p>', "<p class=\"name\">Здравствуйте, {$user->name}.</p>", $mail_content);
                $mail_content = str_replace('<a></a>', "<a href=\"{$link}\">{$link}</a>", $mail_content);

                $this->mail->Body = $mail_content;

                $this->mail->sendWithExceptions();
            } else {
                $this->view->content = 'Неверная почта';
            }
        }
    }


    public function newPasswordAction($uuid)
    {
        $filters = ['email', 'trim', 'striptags', 'string'];
        $email = $this->request->get('email', $filters);

        $reset_password_record = ResetPassword::findFirstByUuid($uuid);

        if (is_null($reset_password_record)) {
            echo '<h1>Ссылка неверная или срок её действия истёк</h1>';
            return false;
        }
        if ($reset_password_record->email !== $email) {
            echo '<h1>Ссылка неверная или срок её действия истёк</h1>';
            return false;
        }

        $this->view->pageTitle = 'Новый пароль';

        if ($this->request->isPost()) {

            $filters = ['trim', 'striptags', 'string'];
            $password = $this->request->getPost('password', $filters, null, true);
            $password_confirm = $this->request->getPost('password-confirm', $filters, null, true);
            if (is_null($password) && is_null($password_confirm)) {
                $this->view->errors = 'Пустое значение';
                // Дальнейший код не выполняется, но при этом ошибка рендерится на сайте
                return true;
            }
            if ($password !== $password_confirm) {
                $this->view->errors = 'Пароли не совпадают';
                // Дальнейший код не выполняется, но при этом ошибка рендерится на сайте
                return true;
            }

            $password = $this->security->hash($password);
            $user = Users::findFirstByEmail($email);
            $user->password = $password;
            if ($user->save()) {
                $reset_password_record->delete("email={$email}");
                $this->response->redirect('/account/login');
            }
            echo 'Ошибка в самом конце (почти) ;(';
            return false;
        }
    }

    public function logoutAction()
    {
        $this->view->pageTitle = 'Выйти';
        $this->response->redirect('/account/login');
        $this->session->destroy();
    }
    public function sendMailDataAction()
    {
        $data = [
            'key' => 'value'
        ];

        return $this->response->setJsonContent($data);
    }
}
