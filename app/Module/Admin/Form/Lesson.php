<?php

namespace App\Module\Admin\Form;

use Light\Filter\Trim;
use Light\Form;
use Light\Validator\StringLength;

class Lesson extends Form
{
    /**
     * @param \App\Model\Lesson $lesson
     */
    public function init($lesson = null)
    {
        parent::init($lesson);

        $this->addElements([

            'Общая информация' => [

                new Form\Element\Text('title', [
                    'value' => $lesson->title,
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
                    'value' => $lesson->webinar,
                    'label' => 'Идентификатор видео',
                    'filters' => [Trim::class]
                ]),

                new Form\Element\Image('file', [
                    'value' => $lesson->file,
                    'label' => 'Файл для скачивания',
                    'allowNull' => true
                ]),

                new Form\Element\Trumbowyg('description', [
                    'value' => $lesson->description,
                    'label' => 'Описание',
                    'filters' => [Trim::class]
                ])
            ],

            'Домашнее задание' => [

                new Form\Element\Trumbowyg('homeTask', [
                    'value' => $lesson->homeTask,
                    'label' => 'Домашнее задание',
                    'filters' => [Trim::class],
                    'allowNull' => true
                ]),
            ],

            'Настройки' => [

                new Form\Element\Select('course', [
                    'value' => $lesson->course,
                    'label' => 'Курс',
                    'options' => \App\Model\Course::fetchAll(),
                    'hint' => 'Выберете курс',
                    'field' => 'title'
                ]),

                new Form\Element\DateTime('start', [
                    'value' => $lesson->start,
                    'label' => 'Дата и время старта занятия',
                ]),

                new Form\Element\Checkbox('enabled', [
                    'value' => $lesson->enabled,
                    'label' => 'Выкл./Вкл.'
                ]),
            ]
        ]);
    }
}
