<?php

namespace App\Module\Api\Map;

use App\Model\HomeTask;
use App\Module\Admin\Controller\Course;
use Light\Map;

class Notification extends Map
{
    /**
     * @return array
     */
    public function common(): array
    {
        return [

            'id' => 'id',

            'dateTime' => 'dateTime',
            'content' => 'content',
            'destination' => 'destination',

            'data' => function (\App\Model\Notification $notification = null) {

                switch ($notification->destination) {

                    case \App\Model\Notification::DEST_HOME_TASK:

                        $homeTask = HomeTask::fetchOne([
                            'user' => $notification->user,
                            'lesson' => $notification->data
                        ]);

                        return Lesson::execute(
                            $homeTask->lesson,
                            'common',
                            [
                                'user' => $notification->user,
                                'course' => \App\Model\Course::fetchOne()
                            ])->toArray();


                    case \App\Model\Notification::DEST_LESSON:

                        return Lesson::execute(
                            \App\Model\Lesson::fetchOne(['id' => $notification->data]),
                            'common',
                            ['user' => $notification->user, 'course' => \App\Model\Course::fetchOne()]
                        )->toArray();


                    case \App\Model\Notification::DEST_ADDITIONAL:

                        return Additional::execute(
                            \App\Model\Additional::fetchOne(['id' => $notification->data]),
                            'common',
                            ['user' => $notification->user]
                        )->toArray();
                }

                return $notification->data;
            }
        ];
    }
}
