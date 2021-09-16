<?php

declare(strict_types = 1);

namespace App\Module\TeacherApi\Controller;

use Light\Map;

use App\Model;

class Notification extends Base
{
    /**
     * @return array|Map
     */
    public function index()
    {
        return \App\Module\TeacherApi\Map\Notification::execute(
            \App\Service\Notification::getInAppNotificationsTeacher()
        );
    }

    /**
     * @return array|Map
     */
    public function item()
    {
        return \App\Module\TeacherApi\Map\Notification::execute(
            Model\TeacherNotification::fetchOne([
                'id' => $this->getParam('notificationId')
            ])
        );
    }

    public function maskAsRead()
    {
        Model\TeacherNotification::remove([
            'id' => $this->getParam('id'),
        ]);
    }
}
