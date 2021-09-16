<?php

declare(strict_types=1);

namespace App\Model;

use Light\Model;

/**
 * @collection Notification
 *
 * @property string $id
 *
 * @property string  $content
 * @property integer $dateTime
 * @property string  $destination
 * @property string  $data
 *
 * @property \App\Model\User $user
 *
 * @method static Notification[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Notification|null fetchOne($cond = null, $sort = null)
 * @method static Notification      fetchObject($cond = null, $sort = null)
 */
class Notification extends Model
{
    const DEST_COURSE = 'course';
    const DEST_LESSON = 'lesson';
    const DEST_HOME_TASK = 'home-task';
    const DEST_CHAT = 'chat';
    const DEST_ADDITIONAL = 'additional';
    const DEST_BILLING = 'billing';
    const DEST_WEBINAR = 'webinar';
}
