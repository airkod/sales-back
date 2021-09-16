<?php

declare(strict_types = 1);

namespace App\Module\Api\Controller;

use App\Model\ChatRoom;
use Light\Filter\HtmlSpecialChars;
use Light\Filter\StripTags;
use Light\Filter\Trim;
use Light\Map;

class Chat extends Base
{
    /**
     * @return array|Map
     */
    public function index()
    {
        \App\Service\Notification::clearChatNotifications($this->user);

        $chat = \App\Model\Chat::fetchAll(
            ['user' => $this->user],
            ['dateTime' => 1]
        );

        return Map::execute($chat, [
            'from' => 'from',
            'message' => 'message',
            'dateTime' => 'dateTime',
            'files' => 'files',
            'teacher' => function (\App\Model\Chat $chat) {

                if ($chat->teacher) {

                    return Map::execute($chat->teacher, [
                        'id' => 'id',
                        'name' => 'name',
                        'image' => 'image'
                    ]);
                }

                return null;
            },
        ]);
    }

    /**
     * @return array|Map
     * @throws \Light\Model\Exception\ConfigWasNotProvided
     */
    public function message()
    {
        $message = new \App\Model\Chat();

        $message->message = $this->getParam('message', null, [
            Trim::class,
            HtmlSpecialChars::class,
            StripTags::class]
        );

        $message->files = $this->getParam('files');
        $message->from = \App\Model\Chat::FROM_USER;
        $message->user = $this->user;
        $message->dateTime = time();

        $message->save();

        $chatRoom = ChatRoom::fetchOne([
            'user' => $this->user
        ]);

        $chatRoom->dateTime = time();
        $chatRoom->readTeacher = false;
        $chatRoom->save();

        \App\Service\Notification::chatUserToTeacher($chatRoom);

        return Map::execute($message, [
            'from' => 'from',
            'message' => 'message',
            'dateTime' => 'dateTime',
            'teacher' => 'teacher',
            'files' => 'files'
        ]);
    }
}
