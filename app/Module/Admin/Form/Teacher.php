<?php

namespace App\Module\Admin\Form;

use Light\Filter\Lowercase;
use Light\Filter\Trim;
use Light\Form;
use Light\Validator\Email;
use Light\Validator\StringLength;

class Teacher extends Form
{
    /**
     * @param \App\Model\Teacher $teacher
     */
    public function init($teacher = null)
    {
        parent::init($teacher);

        $this->addElements([

            'Информация' => [

                new Form\Element\Text('name', [
                    'value' => $teacher->name,
                    'allowNull' => false,
                    'label' => 'Имя',
                    'validators' => [
                        StringLength::class => [
                            'options' => ['min' => 2, 'max' => 50],
                            'message' => 'Имя должен содержать от 2 до 50 символов'
                        ],
                    ],
                    'filters' => [Trim::class]
                ]),

                new Form\Element\Image('image', [
                    'value' => $teacher->image,
                    'label' => 'Аватарка',
                ]),
            ],

            'Данные авторизации' => [

                new Form\Element\Email('email', [
                    'value' => $teacher->email,
                    'label' => 'E-mail',
                    'allowNull' => false,
                    'validators' => [
                        Email::class => [
                            'message' => 'Не валидный E-mail'
                        ]
                    ],
                    'filters' => [Trim::class, Lowercase::class]
                ]),

                new Form\Element\Text('password', [
                    'value' => $teacher->password,
                    'label' => 'Пароль',
                    'filters' => [Trim::class]
                ]),
            ]
        ]);
    }
}
