<?php

declare(strict_types = 1);

namespace App\Module\Admin\Controller;

use Light\Crud;

class Lesson extends Crud
{
    public $title = 'Занятия';

    public $button = true;
    public $positioning = 'title';

    /**
     * @return array
     */
    public function getHeader()
    {
        return [
            'title' => [
                'title' => 'Название',
                'static' => true
            ],
            'start' => [
                'title' => 'Начало',
                'type' => 'datetime',
            ],
            'active' => [
                'title' => 'Активный',
                'source' => function (\App\Model\Lesson $lesson) {

                    return $lesson->isActive() ?

                        '<span class="indicator bmd-bg-success"></span>' :
                        '<span class="indicator bmd-bg-danger"></span>';
                },
            ],
        ];
    }

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
