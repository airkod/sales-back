<?php

declare(strict_types=1);

namespace App\Module\Cli\Controller;

use App\Model\Course;
use App\Model\Lesson;
use App\Model\User;
use App\Model\Webinar;
use App\Service\Notification;
use App\Service\Purchase;
use Light\Front;

class Notifier extends \Light\Controller
{
    /**
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public function startLesson()
    {
        foreach (\App\Model\Lesson::fetchAll(['start' => ['$lt' => time()], 'notified' => false]) as $lesson) {

            Notification::startLesson($lesson);

            $lesson->notified = true;
            $lesson->save();
        }
    }

    /**
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public function applyLessonToUser()
    {
        $course = Course::fetchOne();
        $users = User::fetchAll();

        foreach ($users as $user) {

            if ($user->carteBlanche) {
                continue;
            }

            if (Purchase::isCourseAvailable($user, $course)) {

                $lastLessonStartDate = Purchase::getLastLessonStartDate($user);

                if ($lastLessonStartDate + Front::getInstance()->getConfig()['course']['timeout'] <= time()) {
                    Purchase::applyNextLessonToUser($user);
                }
            }
        }
    }

    /**
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public function startWebinar()
    {
        foreach (Webinar::fetchAll(['enabled' => true, 'notified' => false, 'start' => ['$lt' => time() - 1800]]) as $webinar) {

            Notification::startWebinar($webinar);

            $webinar->notified = true;
            $webinar->save();
        }
    }
}