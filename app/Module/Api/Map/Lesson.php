<?php

namespace App\Module\Api\Map;

use App\Model\Course;
use App\Model\Purchases;
use App\Model\Tariff;
use App\Service\Purchase;
use Light\Map;

class Lesson extends Map
{
    /**
     * @return array
     */
    public function common(): array
    {
        return [

            'id' => 'id',
            'title' => 'title',
            'description' => 'description',
            'homeTask' => 'homeTask',

            'start' => function (\App\Model\Lesson $lesson) {

                /** @var \App\Model\User $user */
                $user = $this->getUserData()['user'];

                /** @var Course $course */
                $course = $this->getUserData()['course'];

                if (!Purchase::isCourseAvailable($user, $course)) {
                    return $lesson->start;
                }

                return Purchase::whatDayTheLessonWillBeAvailable($user, $lesson);
            },

            'webinar' => function (\App\Model\Lesson $lesson) {

                /** @var \App\Model\User $user */
                $user = $this->getUserData()['user'];

                /** @var Course $course */
                $course = $this->getUserData()['course'];

                $isCourseAvailable = Purchase::isCourseAvailable($user, $course);

                if (!$isCourseAvailable) {
                    return null;
                }

                return Purchase::isLessonAvailable($user, $lesson) ? $lesson->webinar : null;
            },

            'active' => function (\App\Model\Lesson $lesson) {

                /** @var \App\Model\User $user */
                $user = $this->getUserData()['user'];

                /** @var Course $course */
                $course = $this->getUserData()['course'];

                if (!Purchase::isCourseAvailable($user, $course)) {
                    return $lesson->isActive();
                }

                if (Purchase::isLessonAvailable($user, $lesson)) {
                    return true;
                }

                return false;
            },

            'file' => function (\App\Model\Lesson $lesson) {

                /** @var \App\Model\User $user */
                $user = $this->getUserData()['user'];

                /** @var Course $course */
                $course = $this->getUserData()['course'];

                $isCourseAvailable = Purchase::isCourseAvailable($user, $course);

                if (!$isCourseAvailable) {
                    return null;
                }

                return Purchase::isLessonAvailable($user, $lesson) ? $lesson->file : null;
            },

            'notification' => function (\App\Model\Lesson $lesson) {

                return \App\Service\Notification::isHomeTaskNotificationExists($lesson, $this->getUserData()['user']) ||
                    \App\Service\Notification::isChatNotificationExists($lesson, $this->getUserData()['user']);
            },

            'hasHomeTask' => function (\App\Model\Lesson $lesson) {
                return \App\Service\Notification::isHomeTaskNotificationExists($lesson, $this->getUserData()['user']);
            },

            'hasChat' => function (\App\Model\Lesson $lesson) {
                return \App\Service\Notification::isChatNotificationExists($lesson, $this->getUserData()['user']);
            },
        ];
    }
}
