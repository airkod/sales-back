<?php

declare(strict_types=1);

namespace App\Model;

use Light\Model;

/**
 * @collection TeacherNotification
 *
 * @property string $id
 *
 * @property string  $content
 * @property integer $dateTime
 * @property string  $destination
 * @property string  $data
 *
 * @method static Notification[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Notification|null fetchOne($cond = null, $sort = null)
 * @method static Notification      fetchObject($cond = null, $sort = null)
 */
class TeacherNotification extends Model
{
    const DEST_HOME_TASK = 'home-task';
    const DEST_CHAT = 'chat';
}
