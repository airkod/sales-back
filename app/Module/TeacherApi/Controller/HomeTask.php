<?php

declare(strict_types=1);

namespace App\Module\TeacherApi\Controller;

use App\Service\Notification;
use Light\Map;
use App\Model;

class HomeTask extends Base
{
    /**
     * @return array|Map
     */
    public function index()
    {
        return \App\Module\TeacherApi\Map\HomeTask::execute(
            Model\HomeTask::fetchAll([], ['createdDateTime' => -1, 'assessmentDateTime' => 1])
        );
    }

    /**
     * @return array|Map
     */
    public function item()
    {
        return \App\Module\TeacherApi\Map\HomeTask::execute(
            Model\HomeTask::fetchOne([
                'id' => $this->getParam('homeTaskId')
            ])
        );
    }

    /**
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public function assessment()
    {
        $homeTask = Model\HomeTask::fetchOne([
            'id' => $this->getParam('id')
        ]);

        $homeTask->assessmentDateTime = time();
        $homeTask->assessment = $this->getParam('assessment');
        $homeTask->comment = $this->getParam('comment');
        $homeTask->status = Model\HomeTask::STATUS_READY;

        $homeTask->user->bonus = $homeTask->user->bonus + $this->getParam('assessment');
        $homeTask->user->save();

        $homeTask->save();

        Notification::homeTaskIsReady($homeTask);

        return \App\Module\TeacherApi\Map\HomeTask::execute($homeTask);
    }
}
