<?php

namespace App\Module\TeacherApi\Map;

use Light\Map;

class HomeTask extends Map
{
    /**
     * @return array
     */
    public function common(): array
    {
        return [

            'id' => 'id',

            'title' => function (\App\Model\HomeTask $homeTask) {
                return $homeTask->user->name;
            },

            'answer' => 'answer',
            'files' => 'files',
            'createdDateTime' => 'createdDateTime',

            'assessment' => 'assessment',
            'assessmentDateTime' => 'assessmentDateTime',
            'comment' => 'comment',

            'status' => 'status',

            'notification' => function (\App\Model\HomeTask $homeTask) {
                return $homeTask->status == \App\Model\HomeTask::STATUS_AWAITING;
            },

            'lesson' => function (\App\Model\HomeTask $homeTask) {

                return Map::execute($homeTask->lesson, [
                    'id' => 'id',
                    'title' => 'title',
                    'description' => 'description',
                    'homeTask' => 'homeTask',
                    'webinar' => 'webinar',
                ]);
            },

            'user' => function (\App\Model\HomeTask $homeTask) {

                return Map::execute($homeTask->user, [
                    'id' => 'id',
                    'name' => 'name'
                ]);
            }
        ];
    }
}
