<?php

declare(strict_types=1);

namespace App\Module\TeacherApi\Controller;

use App\Model\ChatRoom;
use App\Service\Notification;

use Light\Filter\HtmlSpecialChars;
use Light\Filter\StripTags;
use Light\Filter\Trim;
use Light\Map;

class Chat extends Base
{
    /**
     * @return array|Map
     */
    public function rooms()
    {
        return \App\Module\TeacherApi\Map\ChatRoom::execute(
            ChatRoom::fetchAll([], ['dateTime' => -1])
        );
    }

    /**
     * @return array|Map
     */
    public function messages()
    {
        $chatRoom = ChatRoom::fetchOne([
            'id' => $this->getParam('chatRoom')
        ]);

        $chatRoom->readTeacher = true;
        $chatRoom->save();

        Notification::clearChatNotificationsTeacher($chatRoom);

        return Map::execute(

            \App\Model\Chat::fetchAll([
                'user' => $chatRoom->user]
            ), [

                'from' => 'from',
                'message' => 'message',
                'dateTime' => 'dateTime',
                'files' => 'files',
                'teacher' => function (\App\Model\Chat $chat) {
                    return Map::execute($chat->teacher, [
                        'id' => 'id',
                        'name' => 'name',
                        'image' => 'image'
                    ]);
                }
            ]
        );
    }

    /**
     * @return array|Map
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public function message()
    {
        $chatRoom = ChatRoom::fetchOne([
            'id' => $this->getParam('chatRoom')
        ]);

        $message = new \App\Model\Chat();

        $message->message = $this->getParam('message', null, [
            Trim::class,
            HtmlSpecialChars::class,
            StripTags::class
        ]);

        $message->files = $this->getParam('files');
        $message->from = \App\Model\Chat::FROM_TEACHER;
        $message->user = $chatRoom->user;
        $message->teacher = $this->teacher;
        $message->dateTime = time();
        $message->save();

        $chatRoom->dateTime = time();
        $chatRoom->readUser = false;
        $chatRoom->save();

        Notification::chatTeacherToUser($chatRoom);

        return Map::execute($message, [
            'from' => 'from',
            'message' => 'message',
            'dateTime' => 'dateTime',
            'files' => 'files',
            'teacher' => function (\App\Model\Chat $chat) {
                return Map::execute($chat->teacher, [
                    'id' => 'id',
                    'name' => 'name',
                    'image' => 'image'
                ]);
            }
        ]);
    }
}
