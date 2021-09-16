<?php

namespace App\Module\Admin\Controller;

use Light\Crud;
use Light\Form;

class Webinar extends Crud
{
    /**
     * @var string
     */
    public $title = 'Вебинары';

    /**
     * @var string
     */
    public $positioning = 'title';

    public $button = true;

    /**
     * @return array
     */
    public function getHeader()
    {
        return  [
            'title' => [
                'title' => 'Название', 'static' => true
            ],
            'enabled' => [
                'title' => 'Включен', 'type' => 'bool', 'static' => true
            ],
            'start' => [
                'title' => 'Начало',
                'type' => 'datetime',
            ],
            'active' => ['title' => 'Активный', 'source' => function (\App\Model\Webinar $webinar) {
                return $webinar->isActive() ?
                    '<span class="indicator bmd-bg-success"></span>' :
                    '<span class="indicator bmd-bg-danger"></span>';
            }]
        ];
    }

    /**
     * @param \App\Model\Webinar $model
     * @return Form|null
     */
    public function getForm($model = null)
    {
        return new Form(['data' => $model], [

            'Настройки' => [

                new Form\Element\Checkbox('enabled', [
                    'value' => $model->enabled,
                    'label' => 'Включен',
                ]),
                new Form\Element\DateTime('start', [
                    'value' => $model->start,
                    'label' => 'Дата/Время старта',
                ]),
            ],

            'Информация' => [

                new Form\Element\Text('title', [
                    'value' => $model->title,
                    'label' => 'Название',
                ]),

                new Form\Element\Trumbowyg('description', [
                    'value' => $model->description,
                    'label' => 'Описание',
                ]),

                new Form\Element\Text('link', [
                    'value' => $model->link,
                    'label' => 'Ссылка',
                ]),
                new Form\Element\Text('link', [
                    'value' => $model->link,
                    'label' => 'Ссылка',
                ]),
            ],
        ]);
    }
}
