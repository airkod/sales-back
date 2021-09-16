<?php

declare(strict_types=1);

namespace App\Model;

use Light\Model;

/**
 * @collection Chat
 *
 * @property string $id
 *
 * @property string  $from
 * @property string  $message
 * @property array   $files
 * @property integer $dateTime
 *
 * @property \App\Model\User $user
 * @property \App\Model\Teacher $teacher
 *
 * @method static Chat[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Chat|null fetchOne($cond = null, $sort = null)
 * @method static Chat      fetchObject($cond = null, $sort = null)
 */
class Chat extends Model
{
    const FROM_USER = 'user';
    const FROM_TEACHER = 'teacher';
}
