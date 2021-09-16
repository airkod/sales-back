<?php

declare(strict_types=1);

namespace App\Model;

use Light\Model;

/**
 * @collection HomeTask
 *
 * @property string $id
 *
 * @property string  $answer
 * @property array   $files
 * @property integer $createdDateTime
 *
 * @property integer $assessment
 * @property string  $comment
 * @property integer $assessmentDateTime
 *
 * @property string  $status
 *
 * @property \App\Model\Lesson $lesson
 * @property \App\Model\User   $user
 *
 * @method static HomeTask[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static HomeTask|null fetchOne($cond = null, $sort = null)
 * @method static HomeTask      fetchObject($cond = null, $sort = null)
 */
class HomeTask extends Model
{
    const STATUS_AWAITING = 'awaiting';
    const STATUS_READY = 'ready';
}
