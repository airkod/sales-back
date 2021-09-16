<?php

namespace App\Module\Admin\Form;

use Light\Filter\Lowercase;
use Light\Filter\Trim;
use Light\Form;
use Light\Validator\Email;
use Light\Validator\StringLength;

class User extends Form
{
    /**
     * @param \App\Model\User $user
     */
    public function init($user = null)
    {
        parent::init($user);

        $this->addElements([

            'Информация' => [

                new Form\Element\Text('name', [
                    'value' => $user->name,
                    'label' => 'Имя',
                    'validators' => [
                        StringLength::class => [
                            'options' => ['min' => 2, 'max' => 50],
                            'message' => 'Имя должен содержать от 2 до 50 символов'
                        ],
                    ],
                    'filters' => [Trim::class]
                ]),

                new Form\Element\Text('phone', [
                    'value' => $user->phone,
                    'label' => 'Телефон',
                    'filters' => [Trim::class]
                ])
            ],

            'Данные авторизации' => [

                new Form\Element\Email('email', [
                    'value' => $user->email,
                    'label' => 'E-mail',
                    'validators' => [
                        Email::class => [
                            'message' => 'Не валидный E-mail'
                        ]
                    ],
                    'filters' => [Trim::class, Lowercase::class]
                ]),

                new Form\Element\Text('openPassword', [
                    'value' => $user->openPassword,
                    'label' => 'Открытый пароль',
                    'allowNull' => true,
                ]),
            ],

            'Бонусы' => [

                new Form\Element\Number('bonus', [
                    'allowNull' => true,
                    'value' => $user->bonus,
                    'label' => 'Накопленные бонусы',
                ]),

                new Form\Element\Checkbox('carteBlanche', [
                    'value' => $user->carteBlanche,
                    'label' => 'Карт-бланш'
                ])
            ],
        ]);
    }
}
