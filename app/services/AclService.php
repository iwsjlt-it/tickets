<?php

use \Phalcon\Acl\Adapter\Memory as Acl;
use Phalcon\Acl\Component;
use Phalcon\Acl\Role;

class AclService extends Acl
{
    protected $aclFile = APP_PATH . '/services/acl/acl.cache';
    protected $directory = APP_PATH . '/controllers/';
    public function getControllersList()
    {
        $controllers = [];
        foreach ($controllersList =  scandir($this->directory) as $controller) {
            if (str_contains($controller, 'Controller.php')) {
                $controllers[] = str_replace('Controller.php', '', $controller);
            }
        }
        foreach ($controllers as $controller) {
            if ($controller === "." || $controller === ".." || $controller === 'Security' || $controller === 'Access' || $controller === 'Errors') {
                continue;
            }
            $components[strtolower($controller)] = $this->getActionList($controller);
        }
        return $components;
    }
    public function getControllersDesc()
    {
        $controllersDesc = [];
        $files = scandir($this->directory);
        foreach ($files as $file) {
            if ($file === "." || $file === ".." || $file === 'SecurityController.php' || $file === 'AccessController.php' || $file === 'ErrorsController.php') {
                continue;
            }
            $filePath = $this->directory . DIRECTORY_SEPARATOR . $file;
            $readFile = file($filePath);
            foreach ($readFile as $value) {
                if (str_contains($value, '*') && !str_contains($value, '/') && !str_contains($value, '@')) {
                    $controllersDesc[] = trim(str_replace(['*', '/', '"'], '', $value));
                }
            }
        }

        return $controllersDesc;
    }
    public function getActionList($controller)
    {
        $clean_acttions = [];
        $actions = get_class_methods($controller . 'Controller');
        foreach ($actions as $action) {
            if (str_contains($action, 'Action') && !str_contains($action, 'Json')) {
                $clean_acttions[] = str_replace('Action', '', $action);
            }
        }
        return $clean_acttions;
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
    public function initRole($role, $role_desc)
    {
        $acl = $this->load();
        $acl->addRole(new Role($role, $role_desc));
        file_put_contents($this->aclFile, serialize($acl));
    }
    public function removeRole($remove_roles)
    {
        $acl = $this->load();
        $roles = $acl->roles;
        foreach ($remove_roles as $role_name => $value) {
            $role_names[] = $role_name;
        }
        foreach ($roles as $role) {
            if (in_array($role->getName(), $role_names)) {
                continue;
            }
            $clean_roles[$role->getName()] = $role;
        }
        $acl->roles = $clean_roles;
        file_put_contents($this->aclFile, serialize($acl));
    }
    public function setBaseAccess($acl)
    {
        $acl->allow('admin', '*', '*');
        $acl->deny('admin', 'account', ['login', 'signup', 'reset', 'newPassword']);
        $acl->allow('guest', 'account',  ['login', 'signup', 'reset', 'newPassword']);
        $acl->allow('guest', 'index', 'index');
    }
    public function initAcl()
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

        $this->setBaseAccess($acl);

        file_put_contents($this->aclFile, serialize($acl));
        return $acl;
    }

    public function load()
    {
        if (file_exists($this->aclFile)) {
            $acl = unserialize(file_get_contents($this->aclFile));
            return $acl;
        } else {
            return $this->initAcl();
        }
    }
    public function update($accessData)
    {
        try {
            $acl = $this->load();
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
        } catch (\Exception $e) {
            $components = $this->getControllersList();
            foreach ($components as $controller => $actions) {
                $acl->addComponent($controller, $actions);
            }
        }
        $this->setBaseAccess($acl);
        file_put_contents($this->aclFile, serialize($acl));
    }
}
