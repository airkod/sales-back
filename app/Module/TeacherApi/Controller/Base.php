<?php

declare(strict_types=1);

namespace App\Module\TeacherApi\Controller;

use App\Model\Teacher;
use App\Model\TeacherNotification;

use Light\Controller;
use Light\Exception;
use Light\Map;

abstract class Base extends Controller
{
    /**
     * @var Teacher
     */
    public $teacher = null;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        if (isset($_GET['debug'])) {
            $user = Teacher::fetchOne();
            $this->getRequest()->setHeader('x-user', $user->id);
        }

        if ($userId = $this->getRequest()->getHeader('x-user')) {

            $this->teacher = Teacher::fetchOne([
                'id' => $userId
            ]);

            if (!$this->teacher) {
                throw new Exception([], 'User token is invalid');
            }
        } else {
            throw new Exception([], 'User token is empty');
        }
    }

    public function postRun()
    {
        parent::postRun();

        $this->getResponse()->setHeader('x-user', json_encode(
            Map::execute($this->teacher, [
                'id' => 'id',
                'name' => 'name',
                'email' => 'email'
            ])
        ));

        $this->getResponse()->setHeader('x-notifications', json_encode(
            \App\Module\TeacherApi\Map\Notification::execute(
                \App\Service\Notification::getInAppNotificationsTeacher()
            )->toArray()
        ));
    }
}

