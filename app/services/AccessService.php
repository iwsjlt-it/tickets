<?php

use Phalcon\Di\DiInterface;
use Phalcon\Di\InjectionAwareInterface;

class AccessService implements InjectionAwareInterface
{
    /**
     * @var DiInterface
     */
    protected $container;
    protected $acl;
    protected $controller;
    protected $action;
    public function setDi(DiInterface $container): void
    {
        $this->container = $container;
    }

    public function getDi(): DiInterface
    {
        return $this->container;
    }
    public function getUserRole()
    {
        $role = $this->container->get('session')->userRole = (Users::findFirstById($this->container->get('session')->get('userId')))->role ?? 'guest'; // любой кто зайдёт на страницу загрузит сессию
        return $role;
    }
    public function hasAccess(): bool
    {
        $this->acl = (new AclService)->load();
        $this->controller = $this->container->get('dispatcher')->getControllerName();
        $this->action = $this->container->get('dispatcher')->getActionName();
        if ($this->getUserRole() === 'guest') {
            if ($this->acl->isAllowed($this->container->get('session')->userRole, $this->controller, $this->action)) {
                return true;
            } else {
                $this->container->get('dispatcher')->forward([
                    "controller" => "errors",
                    "action"     => "code401",
                ]);
                $this->container->get('response')->setStatusCode(401, 'Unauthorized');
                // $this->container->get('response')->setContent("Для доступа нужно войти в аккаунт");
                return false;
            }
        } else if ($this->acl->isAllowed($this->container->get('session')->userRole, $this->controller, $this->action)) {
            return true;
        } else {
            $this->container->get('dispatcher')->forward([
                "controller" => "errors",
                "action"     => "code403",
            ]);
            $this->container->get('response')->setStatusCode(403, 'Forbidden');
            return false;
        }
    }
}
