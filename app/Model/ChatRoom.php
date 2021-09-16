<?php

declare(strict_types=1);

namespace App\Model;

use Light\Model;

/**
 * @collection ChatRoom
 *
 * @property string $id
 *
 * @property \App\Model\User $user
 *
 * @property boolean $readUser
 * @property boolean $readTeacher
 *
 * @property integer $dateTime
 *
 * @method static ChatRoom[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static ChatRoom|null fetchOne($cond = null, $sort = null)
 * @method static ChatRoom      fetchObject($cond = null, $sort = null)
 */
class ChatRoom extends Model
{
}
