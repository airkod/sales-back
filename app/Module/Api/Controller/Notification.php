<?php

declare(strict_types=1);

namespace App\Module\Api\Controller;

use Light\Map;
use App\Model;

class Notification extends Base
{
    /**
     * @return array|Map
     */
    public function index()
    {
        return \App\Module\Api\Map\Notification::execute(
            \App\Service\Notification::getInAppNotifications($this->user)
        );
    }

    public function item()
    {
        return \App\Module\Api\Map\Notification::execute(
            Model\Notification::fetchOne([
                'id' => $this->getParam('notificationId')
            ])
        );
    }

    public function maskAsRead()
    {
        Model\Notification::remove([
            'id' => $this->getRequest()->getParam('id'),
        ]);
    }
}
