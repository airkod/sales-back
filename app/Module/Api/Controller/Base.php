<?php

declare(strict_types = 1);

namespace App\Module\Api\Controller;

use Light\Controller;
use Light\Exception;
use Light\Map;

abstract class Base extends Controller
{
    /**
     * @var \App\Model\User
     */
    public $user = null;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        if (isset($_GET['debug'])) {
            $this->getRequest()->setHeader('x-user', \App\Model\User::fetchOne()->id);
        }

        if ($userId = $this->getRequest()->getHeader('x-user')) {

            $this->user = \App\Model\User::fetchOne([
                'id' => $userId
            ]);

            if (!$this->user) {
                throw new Exception([], 'User token is invalid', 403);
            }
        }
        else {
            throw new Exception([], 'User token is empty', 403);
        }
    }

    public function postRun()
    {
        parent::postRun();

        $this->getResponse()->setHeader('x-user', json_encode(
            \App\Module\Api\Map\User::execute($this->user)->toArray())
        );

        $this->getResponse()->setHeader('x-notifications', json_encode(
            \App\Module\Api\Map\Notification::execute(
                \App\Service\Notification::getInAppNotifications($this->user)
            )->toArray()
        ));
    }
}

