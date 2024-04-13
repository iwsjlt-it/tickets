<?php

use \Phalcon\Acl\Adapter\Memory as Acl;
use Phalcon\Acl\Component;

class AclService extends Acl
{
    protected $aclFile = APP_PATH . '/services/acl.cache';
    public function getControllersAndActions()
    {
        $controllers_dir = array_diff(scandir('D:\OSPanel\domains\tickets\app\controllers'), array('.', '..'));
        $controllers = array();
        foreach ($controllers_dir as $controller) {
            $controllers[] = array_diff(explode('Controller.php', $controller), array(''));
        }
        $controllers = call_user_func_array('array_merge', $controllers);

        foreach ($controllers as $controller) {
            $controllers[][] = array_diff(get_class_methods($controller . 'Controller'), array('BeforeExecuteRoute', '__construct','__get','__isset','getDI','setDI'));
        }
        //$actions = call_user_func_array('array_merge', $actions);
    }
    protected function save()
    {
        $acl = new Acl();
        $acl->addRole('admin');
        $acl->addRole('employee');
        $acl->addRole('client');
        $acl->addRole('guest');

        $acl->addComponent(
            'index',
            [
                'index'
            ]
        );
        $acl->addComponent(
            'account',
            [
                'index',
                'login',
                'signup',
                'recovery',
                'logout'
            ]
        );
        $acl->addComponent(
            'users',
            [
                'index',
                'list',
                'delete',
                'update'
            ]
        );
        $acl->addComponent(
            'line',
            [
                'list',
                'edit',
                'catch'
            ]
        );
        $acl->addComponent(
            'tickets',
            [
                'index',
                'creation',
                'templates',
                'viewlistModal',
                'editticket',
                'update',
                'viewlist',
                'send'
            ]
        );
        $acl->addComponent(
            'contact',
            [
                'list',
                'edit',
                'update'
            ]
        );
        $acl->addComponent(
            'firm',
            [
                'list',
                'send',
                'edit'
            ]
        );
        $acl->addComponent(
            'info',
            [
                'index',
                'editlist',
                'viewlist',
                'aboutlist',
                'formlist',
                'send'
            ]
        );
        $acl->addComponent(
            'workprogress',
            [
                'index',
                'send'
            ]
        );
        $acl->addComponent(
            'access',
            [
                'index'
            ]
        );
        // Базовый доступ
        $acl->allow('admin', '*', '*');
        $acl->deny('admin', 'account', ['login', 'signup', 'recovery']);
        $acl->allow('guest', 'account',  ['login', 'signup', 'recovery']);

        file_put_contents($this->aclFile, serialize($acl));
        return $acl;
    }
    public function load()
    {
        if (file_exists($this->aclFile)) {
            $acl = unserialize(file_get_contents($this->aclFile));
            return $acl;
        } else {
            return $this->save();
        }
    }
    public function update($data)
    {
        $acl = $this->load();
        $acl->access = array();
        foreach ($data as $key => $value) {
            $acl->access[$key] = (int)$value;
        }
        $acl->allow('admin', '*', '*');
        $acl->deny('admin', 'account', ['login', 'signup', 'recovery']);
        $acl->allow('guest', 'account',  ['login', 'signup', 'recovery']);
        file_put_contents($this->aclFile, serialize($acl));
    }
}
