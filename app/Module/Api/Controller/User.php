<?php

declare(strict_types=1);

namespace App\Module\Api\Controller;

use Light\Exception;
use Light\Filter\HtmlSpecialChars;
use Light\Filter\Lowercase;
use Light\Filter\Trim;
use Light\Form;
use Light\Map;
use Light\Validator\Email;
use Light\Validator\StringLength;

class User extends Base
{
    public function index()
    {
        return \App\Module\Api\Map\User::execute($this->user, 'common');
    }

    /**
     * @return array|Map
     *
     * @throws Exception
     * @throws Exception\ValidatorClassWasNotFound
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public function update()
    {
        $form = new Form();

        $user = $this->user;

        $form->addElements([

            new Form\Element\Text('name', [
                'validators' => [
                    StringLength::class => [
                        'options' => ['min' => 2, 'max' => 50],
                        'message' => 'Имя должен содержать от 2 до 50 символов'
                    ],
                ],
                'filters' => [Trim::class, HtmlSpecialChars::class]
            ]),

            new Form\Element\Text('phone', [
                'validators' => [
                    StringLength::class => [
                        'options' => ['min' => 5, 'max' => 20],
                        'message' => 'Телефон должен содержать от 2 до 20 символов'
                    ],
                ],
                'filters' => [Trim::class, HtmlSpecialChars::class],
            ]),

            new Form\Element\Text('email', [
                'validators' => [
                    Email::class => [
                        'message' => 'Не корректный E-mail адрес'
                    ],
                    'exists' => [
                        'isValid' => function ($value) use ($user) {

                            if (!$value) {
                                return true;
                            }

                            return !\App\Model\User::count([
                                'email' => strtolower(trim(\htmlspecialchars($value))),
                                '_id' => ['$ne' => new \MongoDB\BSON\ObjectId(
                                    $user->id
                                )]
                            ]);
                        },
                        'message' => 'E-mail адрес уже зарезервирован'
                    ]
                ],
                'filters' => [Trim::class, Lowercase::class, HtmlSpecialChars::class],
            ])
        ]);

        if ($form->isValid($this->getRequest()->getParams())) {

            $user->populate($form->getValues());
            $user->save();

            return \App\Module\Api\Map\User::execute($user, 'common');
        }

        throw new Exception($form->getErrorMessages(), 'Проверьте данные', 400);
    }

    /**
     * @return array
     */
    public function deviceToken()
    {
        $this->user->token = $this->getRequest()->getPost('token', null, [Trim::class]);
        $this->user->save();

        return [];
    }


    /**
     * @return array
     * @throws Exception
     */
    public function password()
    {
        $oldPassword = $this->getRequest()->getParam('old-password', null, [Trim::class]);
        $newPassword = $this->getRequest()->getParam('new-password', null, [Trim::class]);

        $passwordValidator = new StringLength([
            'min' => 6,
            'max' => 20
        ]);

        if (!$passwordValidator->isValid($oldPassword)) {
            throw new Exception(['old' => 'Введите текущий пароль.']);
        }

        if ($this->user->password != md5($oldPassword)) {
            throw new Exception(['old' => 'Текущий пароль введен не верно.']);
        }

        if (!$passwordValidator->isValid($newPassword)) {
            throw new Exception(['new' => 'Пароль должен содержать от 6 до 20 символов.']);
        }

        $this->user->password = md5($newPassword);
        $this->user->save();

        return [];
    }
}
