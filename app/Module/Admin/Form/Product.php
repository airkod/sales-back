<?php

namespace App\Module\Admin\Form;

use Light\Filter\Trim;
use Light\Form;
use Light\Validator\StringLength;

class Product extends Form
{
    /**
     * @param \App\Model\Product $product
     */
    public function init($product = null)
    {
        parent::init($product);

        $this->addElements([

            'Общая информация' => [

                new Form\Element\Text('title', [
                    'value' => $product->title,
                    'allowNull' => false,
                    'label' => 'Название',
                    'validators' => [
                        StringLength::class => [
                            'options' => ['min' => 2, 'max' => 250],
                            'message' => 'Название должно содержать от 2 до 250 символов'
                        ],
                    ],
                    'filters' => [Trim::class]
                ]),

                new Form\Element\Image('image', [
                    'value' => $product->image,
                    'label' => 'Изображение',
                    'allowNull' => false
                ]),

                new Form\Element\Textarea('smallDescription', [
                    'value' => $product->smallDescription,
                    'allowNull' => false,
                    'label' => 'Краткое описание',
                    'filters' => [Trim::class]
                ]),

                new Form\Element\Trumbowyg('description', [
                    'value' => $product->description,
                    'allowNull' => false,
                    'label' => 'Подробное описание',
                    'filters' => [Trim::class]
                ]),
            ],

            'Настройки' => [

                new Form\Element\Number('price', [
                    'value' => $product->price,
                    'label' => 'Стоимость',
                    'allowNull' => false,
                ]),

                new Form\Element\Checkbox('enabled', [
                    'value' => $product->enabled,
                    'label' => 'Выключен'
                ])
            ]
        ]);
    }
}
