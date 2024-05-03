<?php

declare(strict_types=1);
/**
 * Аккаунт
 */
use Phalcon\Mvc\Controller;
class SecurityController extends Controller
{
        public function BeforeExecuteRoute()
        {
                if ($this->access->hasAccess()) {
                        return true;
                } else {
                       return false;
                }
                //         if ($role === 'guest') {
                //                 if ($acl->isAllowed($role, $controller, $action)) {
                //                         return true;
                //                 } else {
                //                         $this->dispatcher->forward([
                //                                 "controller" => "errors",
                //                                 "action"     => "code401",
                //                         ]);
                //                         $this->response->setStatusCode(401, 'Forbidden');
                //                         $this->response->setContent("Для доступа нужно войти в аккаунт");
                //                         return false;
                //                 }
                //         } else if ($acl->isAllowed($role, $controller, $action)) {
                //                 return true;
                //         } else {
                //                 $this->dispatcher->forward([
                //                         "controller" => "errors",
                //                         "action"     => "code403",
                //                 ]);
                //                 return false;
                //         }
        }
}
