<?php

namespace App\Module\Admin\Controller;

use Light\CrudSingle;
use Light\Filter\Trim;
use Light\Form;
use Light\Validator\StringLength;

/**
 * Class Course
 * @package App\Module\Admin\Controller
 */
class Course extends CrudSingle
{
    /**
     * @var string
     */
    public $title = 'Описание курса';

    /**
     * @param \App\Model\Course $model
     * @return Form|null
     */
    public function getForm($model = null)
    {
        return new Form(['data' => $model], [

            new Form\Element\Text('title', [
                'value' => $model->title,
                'allowNull' => false,
                'label' => 'Название курса',
                'validators' => [
                    StringLength::class => [
                        'options' => ['min' => 2, 'max' => 250],
                        'message' => 'Название курса должно содержать от 2 до 250 символов'
                    ],
                ],
                'filters' => [Trim::class]
            ]),

            new Form\Element\Textarea('description', [
                'value' => $model->description,
                'allowNull' => false,
                'label' => 'Краткое описание курса',
                'filters' => [Trim::class]
            ]),

            new Form\Element\Trumbowyg('content', [
                'value' => $model->content,
                'allowNull' => false,
                'label' => 'Подробное описание курса',
                'filters' => [Trim::class]
            ]),
        ]);
    }
}
