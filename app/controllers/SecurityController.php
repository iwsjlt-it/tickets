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
                return $this->access->hasAccess();
                        
        }
}
