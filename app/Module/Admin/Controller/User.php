<?php

namespace App\Module\Admin\Controller;

use App\Model\Course;
use App\Service\Purchase;
use Light\Crud;

class User extends Crud
{
    public $title = 'Управление пользователями';
    public $export = 'Экпорт';

    public $button = true;

    public $filter = [
        ['type' => 'search', 'by' => ['email', 'name', 'phone']],
    ];

    /**
     * @return array
     */
    public function getHeader()
    {
        return [
            'name' =>  ['title' => 'Имя', 'static' => true],
            'email' => ['title' => 'E-mail', 'type' => 'email'],
            'phone' => ['title' => 'Телефон', 'type' => 'phone'],
            'bonus' => ['title' => 'Бонус'],
            'active' => ['title' => 'Подписанный', 'source' => function (\App\Model\User $user) {

                return Purchase::isCourseAvailable($user, Course::fetchOne()) ?

                    '<span class="indicator bmd-bg-success"></span>' :
                    '<span class="indicator bmd-bg-danger"></span>';
            }]
        ];
    }

    public function apply()
    {
        $user = \App\Model\User::fetchOne([
            'email' => $this->getParam('email')
        ]);

        $tariff = \App\Model\Tariff::fetchOne([
            'type' => $this->getParam('type')
        ]);

        $user->bonus = $user->bonus + $tariff->price;
        $user->save();

        Purchase::buyTariff($user, $tariff);

        die("OK");
    }
}
