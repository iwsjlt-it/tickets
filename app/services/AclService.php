<?php

use \Phalcon\Acl\Adapter\Memory as Acl;
use Phalcon\Acl\Component;
use Phalcon\Acl\Role;

class AclService extends Acl
{
    protected $aclFile = APP_PATH . '/services/acl.cache';
    protected $directory = APP_PATH . '/controllers/';

    public function getControllersList()
    {
        $controllers = str_replace('Controller.php', '', scandir($this->directory));
        foreach ($controllers as $controller) {
            if ($controller === "." || $controller === ".." || $controller === 'Security' || $controller === 'Access') {
                continue;
            }
            $components[strtolower($controller)] = $this->getActionList($controller);
        }
        return $components;
    }
    public function getControllersDesc()
    {
        if ($handle = opendir($this->directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file === "." || $file === ".." || $file === 'SecurityController.php' || $file === 'AccessController.php') {
                    continue;
                }
                $filePath = $this->directory . DIRECTORY_SEPARATOR . $file;
                $readFile = file($filePath);
                foreach ($readFile as $value) {
                    if (str_contains($value, '*') && !str_contains($value, '/')) {
                        $controllersDesc[] = trim(str_replace(['*', '/', '"'], '', $value));
                    }
                }
            }
            closedir($handle);
        }
        return $controllersDesc;
    }
    public function getActionList($controller)
    {
        $actions = array_diff(get_class_methods($controller . 'Controller'), array('BeforeExecuteRoute', '__construct', '__get', '__isset', 'getDI', 'setDI'));
        $actions = str_replace('Action', '', $actions);
        return $actions;
    }
    public function getRolesDesc($acl)
    {
        $objectRoles = $acl->getRoles();
        foreach ($objectRoles as $role) {
            if ($role->getName() !== 'admin') {
                $roles_desc[] = $role->getDescription();
            }
        }
        return $roles_desc;
    }
    public function getRolesList($acl)
    {
        $objectRoles = $acl->getRoles();
        foreach ($objectRoles as $role) {
            if ($role->getName() !== 'admin') {
                $roles[] = $role->getName();
            }
        }
        return $roles;
    }
    public function getAllowList()
    {
        $acl = $this->load();
        $roles = $this->getRolesList($acl);
        $controllers = $this->getControllersList();

        $allow_list = [];
        foreach ($roles as $role) {
            foreach ($controllers as $controller => $actions) {
                foreach ($actions as $action) {
                    $allow_list[$role][$controller][$action] = $acl->isAllowed($role, $controller, $action);
                }
            }
        }
        return $allow_list;
    }
    public function initRole()
    {
    }
    public function removeRole()
    {
    }
    public function create()
    {
        $acl = new Acl();
        $acl->addRole(new Role('admin', 'Админ'));
        $acl->addRole(new Role('guest', 'Гость'));
        $acl->addRole(new Role('client', 'Клиент'));
        $acl->addRole(new Role('employee', 'Сотрудник'));

        $components = $this->getControllersList();
        foreach ($components as $controller => $actions) {
            $acl->addComponent($controller, $actions);
        }
        // Базовый доступ
        $acl->allow('admin', '*', '*');
        $acl->deny('admin', 'account', ['login', 'signup', 'recovery']);
        $acl->allow('guest', 'account',  ['login', 'signup', 'recovery']);
        $acl->allow('guest', 'index', 'index');

        file_put_contents($this->aclFile, serialize($acl));
        return $acl;
    }

    public function load()
    {
        if (file_exists($this->aclFile)) {
            $acl = unserialize(file_get_contents($this->aclFile));
            return $acl;
        } else {
            return $this->create();
        }
    }
    public function update($accessData)
    {
        $acl = $this->load();
        // $acl->access = array();
        // foreach ($accessData as $key => $value) {
        //     $acl->access[$key] = (int)$value;
        // }
        $acl->access = array();
        foreach ($accessData as $role => $controllers) {
            foreach ($controllers as $controller => $actions) {
                foreach ($actions as $action => $access) {
                    if ($access === true) {
                        $acl->allow($role, $controller, $action);
                    }
                }
            }
        }
        $acl->allow('admin', '*', '*');
        $acl->deny('admin', 'account', ['login', 'signup', 'recovery']);
        $acl->allow('guest', 'account',  ['login', 'signup', 'recovery']);
        file_put_contents($this->aclFile, serialize($acl));
    }
}
