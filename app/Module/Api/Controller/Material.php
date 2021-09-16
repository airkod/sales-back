<?php

declare(strict_types=1);

namespace App\Module\Api\Controller;

use App\Model\Additional;
use App\Model\Course;
use App\Model\HomeTask;
use App\Model\Lesson;
use App\Model\Tariff;
use App\Model\Webinar;
use App\Service\Purchase;
use Light\Map;

class Material extends Base
{
    /**
     * @return array|Map
     */
    public function tariff()
    {
        return Map::execute(
            Tariff::fetchAll(), [
                'type' => 'type',
                'title' => 'title',
                'description' => 'description',
                'price' => 'price',
                'popular' => 'popular'
            ]
        );
    }

    /**
     * @return array|Map
     */
    public function course()
    {
        return Map::execute(
            \App\Model\Course::fetchOne(), [
                'title' => 'title',
                'description' => 'description',
                'content' => 'content'
            ]
        );
    }

    /**
     * @return array|Map
     */
    public function lessons()
    {
        return \App\Module\Api\Map\Lesson::execute(
            Lesson::fetchAll(['enabled' => true], ['position' => 1]),
            'common',
            ['user' => $this->user, 'course' => Course::fetchOne()]
        );
    }

    /**
     * @return array|Map
     */
    public function webinar()
    {
        $user = $this->user;

        return Map::execute(

            Webinar::fetchAll(['enabled' => true], ['position' => 1]), [

                'id' => 'id',
                'title' => 'title',
                'description' => 'description',
                'start' => 'start',

                'active' => function (Webinar $webinar) {
                    return $webinar->isActive();
                },

                'link' => function (Webinar $webinar) use ($user) {

                    return Purchase::isWebinarAvailable($user, $webinar) ?
                        $webinar->link :
                        null;
                }
            ]
        );
    }

    /**
     * @return array|Map
     */
    public function additional()
    {
        return \App\Module\Api\Map\Additional::execute(
            Additional::fetchAll(
                ['enabled' => true],
                ['position' => 1]
            ),
            'common' ,
            ['user' => $this->user]
        );
    }

    /**
     * @return array|Map
     */
    public function homeTask()
    {
        $homeTask = HomeTask::fetchOne([
            'user' => $this->user,
            'lesson' => $this->getParam('lesson')
        ]);

        return Map::execute($homeTask, [
                'answer' => 'answer',
                'files' => 'files',
                'createdDateTime' => 'createdDateTime',
                'assessment' => 'assessment',
                'comment' => 'comment',
                'assessmentDateTime' => 'assessmentDateTime',
                'status' => 'status'
            ]
        );
    }

    /**
     * @return array|Map
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public function submitHomeTask()
    {
        $homeTask = new HomeTask();

        $homeTask->user = $this->user;
        $homeTask->status = HomeTask::STATUS_AWAITING;
        $homeTask->lesson = $this->getParam('lesson');

        $homeTask->answer = $this->getParam('answer');
        $homeTask->files = $this->getParam('files');
        $homeTask->createdDateTime = time();

        $homeTask->save();

        \App\Service\Notification::homeTaskIsAwaiting($homeTask);

        return Map::execute($homeTask, [
            'answer' => 'answer',
            'files' => 'files',
            'createdDateTime' => 'createdDateTime',
            'assessment' => 'assessment',
            'comment' => 'comment',
            'assessmentDateTime' => 'assessmentDateTime',
            'status' => 'status'
        ]);
    }
}
