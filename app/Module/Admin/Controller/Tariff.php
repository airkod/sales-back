<?php

namespace App\Module\Admin\Controller;

use Light\Crud;
use Light\Form;

class Tariff extends Crud
{
    /**
     * @var string
     */
    public $title = 'Тарифы';

    /**
     * @var array
     */
    public $header = [
        'title' => ['title' => 'Название', 'static' => true],
        'price' => ['title' => 'Цена'],
    ];

    /**
     * @param \App\Model\Tariff $model
     * @return Form|null
     */
    public function getForm($model = null)
    {
        return new Form(['data' => $model], [

            'Информация' => [

                new Form\Element\Text('title', [
                    'value' => $model->title,
                    'label' => 'Название',
                ]),

                new Form\Element\Trumbowyg('description', [
                    'value' => $model->description,
                    'label' => 'Описание',
                ]),

                new Form\Element\Checkbox('popular', [
                    'value' => $model->popular,
                    'label' => 'Пометить как популярный'
                ]),

                new Form\Element\Number('price', [
                    'value' => $model->price,
                    'label' => 'Цена',
                ]),
            ],
        ]);
    }
}
