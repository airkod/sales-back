<?php

declare(strict_types = 1);

namespace App\Module\Api\Controller;

use App\Model\ChatRoom;
use App\Service\Notification;

use App\Service\WayForPay;
use Light\Controller;
use Light\Exception;
use Light\Filter\HtmlSpecialChars;
use Light\Filter\Lowercase;
use Light\Filter\Trim;
use Light\Form;
use Light\Map;
use Light\Validator\Email;
use Light\Validator\StringLength;

class Account extends Controller
{
    /**
     * @return array|Map
     *
     * @throws Exception
     */
    public function auth()
    {
        $email = $this->getRequest()->getParam('email', null, [Trim::class, Lowercase::class]);

        $user = \App\Model\User::fetchOne([
            'email' => $email,
            'password' => md5($this->getRequest()->getParam('password', null, [Trim::class]))
        ]);

        if (!$user) {

            $user = \App\Model\User::fetchOne([
                'email' => $email,
                'openPassword' => $this->getRequest()->getParam('password', null, [Trim::class])
            ]);

            if (!$user) {
                throw new Exception([], 'Проверьте данные формы авторизации', 400);
            }
        }

        return \App\Module\Api\Map\User::execute($user, 'common');
    }

    /**
     * @return array|Map
     *
     * @throws Exception
     * @throws Exception\ValidatorClassWasNotFound
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public function register()
    {
        $form = new Form();

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
                        'isValid' => function ($value) {
                            return !\App\Model\User::count([
                                'email' => strtolower(trim(\htmlspecialchars($value)))
                            ]);
                        },
                        'message' => 'E-mail адрес уже зарезервирован'
                    ]
                ],
                'filters' => [Trim::class, Lowercase::class, HtmlSpecialChars::class],
            ]),

            new Form\Element\Text('password', [
                'validators' => [
                    StringLength::class => [
                        'options' => ['min' => 6, 'max' => 20],
                        'message' => 'Пароль должен содержать от 6 до 20 символов'
                    ]
                ],
                'filters' => [Trim::class]
            ])
        ]);

        if ($form->isValid($this->getRequest()->getParams())) {

            $data = $form->getValues();

            $data['password'] = md5($data['password']);

            $user = new \App\Model\User();
            $user->populate($data);
            $user->save();

            $chatRoom = new ChatRoom();
            $chatRoom->user = $user;
            $chatRoom->readUser = true;
            $chatRoom->readTeacher = true;
            $chatRoom->save();

            return \App\Module\Api\Map\User::execute($user, 'common');
        }

        throw new Exception($form->getErrorMessages(), 'Проверьте данные формы регистрации', 400);
    }

    /**
     * @throws Exception
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function forgotPassword()
    {
        $user = \App\Model\User::fetchOne([
            'email' => $this->getParam('email', null, [Trim::class, Lowercase::class]),
        ]);

        if (!$user) {
            throw new Exception([], 'E-mail адрес не найден', 400);
        }

        $newPassword = uniqid();

        $user->password = md5($newPassword);
        $user->openPassword = $newPassword;
        $user->save();

        Notification::sendNewPassword($user, $newPassword);
    }
}
