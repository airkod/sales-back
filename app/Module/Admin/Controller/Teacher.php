<?php

namespace App\Module\Admin\Controller;

use Light\Crud;

class Teacher extends Crud
{
    public $title = 'Управление преподавателями';

    public $button = true;

    public $header = [
        'image' => ['title' => 'Аватарка', 'type' => 'image', 'static' => true],
        'name' =>  ['title' => 'Имя', 'static' => true],
        'email' => ['title' => 'E-mail', 'type' => 'email'],
    ];

    public $filter = [
        ['type' => 'search', 'by' => ['email', 'name']],
    ];
}
