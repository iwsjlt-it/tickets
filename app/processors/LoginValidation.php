<?php

use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\PresenceOf;

class LoginValidation extends Validation
{
    public function initialize()
    {
        $this->add(
            [
                'email', 'password'
            ],
            new PresenceOf(
                [
                    'message' => 'Поле пустое',
                    'cancelOnFail' => true
                ]
            )
        );

        $this->setFilters([
            'first_name', 'last_name', 'login', 'email', 'password', 'confirmpassword'
        ], [
            'trim', 'striptags', 'string'
        ]);
    }
}
