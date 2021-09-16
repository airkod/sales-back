<?php

namespace App\Module\Admin\Form;

use Light\Filter\Trim;
use Light\Form;
use Light\Validator\StringLength;

class Additional extends Form
{
    /**
     * @param \App\Model\Additional $additional
     */
    public function init($additional = null)
    {
        parent::init($additional);

        $this->addElements([

            'Общая информация' => [

                new Form\Element\Text('title', [
                    'value' => $additional->title,
                    'label' => 'Название занятия',
                    'validators' => [
                        StringLength::class => [
                            'options' => ['min' => 2, 'max' => 250],
                            'message' => 'Название должно содержать от 2 до 250 символов'
                        ],
                    ],
                    'filters' => [Trim::class]
                ]),

                new Form\Element\Text('webinar', [
                    'value' => $additional->webinar,
                    'label' => 'Идентификатор видео',
                    'filters' => [Trim::class]
                ]),

                new Form\Element\Image('file', [
                    'value' => $additional->file,
                    'label' => 'Файл для скачивания',
                    'allowNull' => true
                ]),

                new Form\Element\Textarea('advertisingDescription', [
                    'value' => $additional->advertisingDescription,
                    'label' => 'Рекламный текст',
                    'filters' => [Trim::class]
                ]),

                new Form\Element\Trumbowyg('description', [
                    'value' => $additional->description,
                    'label' => 'Описание',
                    'filters' => [Trim::class]
                ])
            ],

            'Настройки' => [

                new Form\Element\Checkbox('enabled', [
                    'value' => $additional->enabled,
                    'label' => 'Включен'
                ]),

                new Form\Element\Number('price', [
                    'value' => $additional->price,
                    'label' => 'Стоимость'
                ]),

                new Form\Element\Select('course', [
                    'value' => $additional->course,
                    'label' => 'Курс',
                    'options' => \App\Model\Course::fetchAll(),
                    'hint' => 'Выберете курс',
                    'field' => 'title'
                ]),
            ]
        ]);
    }
}
