<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Autoload\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Session\Manager;


define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '\app');

$loader = new Loader();

$loader->setDirectories(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/processors/',
        APP_PATH . '/services/'
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => 'd:\OSPanel\userdata\temp',
            ]
        );

        $session
            ->setAdapter($files)
            ->start();

        return $session;
    }
);


$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => '127.0.0.1',
                'username' => 'root',
                'password' => '',
                'dbname'   => 'register-bd',
            ]
        );
    }
);

$container->set(
    'access',
    function () {
        return new AccessService();
    }
);

$container->set(
    'mail',
    function () {
        $mail_config = parse_ini_file(APP_PATH . '/services/phpmailer/mail.ini');
        return new MailService($mail_config);
    }
);


$application = new Application($container);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
