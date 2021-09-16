<?php

declare(strict_types = 1);

namespace App\Module\Admin\Controller;

use Light\Crud;

class Product extends Crud
{
    public $title = 'Продукты';

    public $button = true;
    public $positioning = 'title';

    public $header = [
        'image' => ['title' => 'Рис.', 'type' => 'image', 'static' => true],
        'title' => ['title' => 'Название', 'static' => true],
        'enabled' => ['title' => 'Включен', 'type' => 'bool'],
    ];
}
