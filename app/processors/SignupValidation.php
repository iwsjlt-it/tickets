<?php

use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\Validator\Alpha;
use Phalcon\Filter\Validation\Validator\Confirmation;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\Regex;
use Phalcon\Filter\Validation\Validator\StringLength;
use Phalcon\Filter\Validation\Validator\Uniqueness;

class SignupValidation extends Validation
{

    public function initialize()
    {
        $this->add(
            [
                'first_name', 'last_name', 'email', 'password', 'confirmpassword'
            ],
            new PresenceOf(
                [
                    'message' => 'Поле пустое',
                    'cancelOnFail' => true
                ]
            )
        );
        $this->add(
            [
                'first_name', 'last_name'
            ],
            new Alpha(
                [
                    'message' => 'Допустимы только буквы',
                    'cancelOnFail' => true

                ]
            )
        );

        $this->add(
            'email',
            new Email(
                [
                    'message' => 'Неправильная почта',
                    'cancelOnFail' => true
                ]
            )
        );

        $this->add(
            "password",
            new Confirmation(
                [
                    "message" => "Пароли не совпадают",
                    "with"    => "confirmpassword",
                    'cancelOnFail' => true
                ]
            )
        );
        $this->add(
            [
                'first_name', 'last_name'
            ],
            new StringLength(
                [
                    "max"             => 30,
                    "min"             => 2,
                    "messageMaximum"  => "Слишком длинное значение",
                    "messageMinimum"  => "Слишком маленькое значение",
                    "includedMaximum" => true,
                    "includedMinimum" => false,
                    'cancelOnFail' => true
                ]
            )
        );
        $this->add(
            'email',
            new Uniqueness(
                [
                    "model"   => new Users(),
                    "message" => "Почта уже существует",
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
