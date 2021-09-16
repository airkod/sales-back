<?php

namespace App\Module\TeacherApi\Map;

use App\Module\Api\Map\User;
use Light\Map;

class ChatRoom extends Map
{
    /**
     * @return array
     */
    public function common(): array
    {
        return [

            'id' => 'id',

            'title' => function (\App\Model\ChatRoom $chatRoom) {
                return $chatRoom->user->name;
            },

            'user' => function (\App\Model\ChatRoom $chatRoom) {
                return User::execute($chatRoom->user)->toArray();
            },

            'notification' => function (\App\Model\ChatRoom $chatRoom) {
                return $chatRoom->readTeacher == false;
            },
        ];
    }
}
