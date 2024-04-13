<?php

declare(strict_types=1);

use Phalcon\Mvc\Controller;

class SecurityController extends Controller
{
        public function BeforeExecuteRoute()
        {
                $acl = (new AclService)->load();
                $this->session->userRole = (Users::findFirstById($this->session->get('userId')))->role ?? 'guest'; // любой кто зайдёт на страницу загрузит сессию
                $controller = $this->dispatcher->getControllerName();
                $action =  $this->dispatcher->getActionName();
                
                if ($this->session->userRole === 'guest') {
                        if ($acl->isAllowed($this->session->userRole, $controller, $action)) {
                                return true;
                        } else {
                                $this->dispatcher->forward([
                                        "controller" => "account",
                                        "action"     => "login",
                                ]);
                                $this->response->setStatusCode(403, 'Forbidden');
                                $this->response->setContent("Для доступа нужно войти в аккаунт");
                                return false;
                        }
                } else if ($acl->isAllowed($this->session->userRole, $controller, $action)) {
                        return true;
                } else {
                        $this->dispatcher->forward([
                                "controller" => "errors",
                                "action"     => "code403",
                        ]);
                        return false;
                }
        }
}
