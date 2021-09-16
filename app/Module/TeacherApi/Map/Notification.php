<?php

namespace App\Module\TeacherApi\Map;

use App\Model\ChatRoom;
use App\Model\TeacherNotification;
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

            'data' => function (TeacherNotification $teacherNotification) {

                if ($teacherNotification->destination == TeacherNotification::DEST_CHAT) {

                    return \App\Module\TeacherApi\Map\ChatRoom::execute(

                        ChatRoom::fetchOne(
                            ['id' => $teacherNotification->data],
                            ['dateTime' => -1]
                        )
                    )->toArray();
                }

                if ($teacherNotification->destination == TeacherNotification::DEST_HOME_TASK) {

                    return HomeTask::execute(
                        \App\Model\HomeTask::fetchOne([
                            'id' => $teacherNotification->data
                        ])
                    )->toArray();
                }

                return $teacherNotification->data;
            }
        ];
    }
}
