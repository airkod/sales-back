<?php

declare(strict_types = 1);

namespace App\Module\Admin\Controller;

use Light\Crud;

class Additional extends Crud
{
    public $title = 'Дополнительные материалы';

    public $button = true;
    public $positioning = 'title';

    public $header = [
        'title' => ['title' => 'Название', 'static' => true],
        'enabled' => ['title' => 'Включен', 'type' => 'bool', 'static' => true]
    ];

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return [
            ['type' => 'search', 'by' => ['title', 'content', 'description']],
            ['type' => 'model', 'by' => 'course', 'model' => \App\Model\Course::fetchAll(), 'field' => 'title'],
        ];
    }
}
