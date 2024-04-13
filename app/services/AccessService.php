<?php

use Phalcon\Di\DiInterface;
use Phalcon\Di\InjectionAwareInterface;

class AccessService implements InjectionAwareInterface
{
    /**
     * @var DiInterface
     */
    protected $container;

    public function setDi(DiInterface $container): void
    {
        $this->container = $container;
    }

    public function getDi(): DiInterface
    {
        return $this->container;
    }


    public function hasLogin():bool
    {
         return $this->container->get('session')->has('userId');
    }

    public function hasAccess(string $resource_name)
    {
        return $resource_name === 'users';
    }
}
