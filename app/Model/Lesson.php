<?php

declare(strict_types=1);

namespace App\Model;

use Light\Model;

/**
 * @collection Lesson
 *
 * @property string $id
 *
 * @property string $title
 * @property string $description
 * @property string $homeTask
 * @property string $webinar
 * @property string $file
 *
 * @property \App\Model\Course $course
 * @property integer $start
 *
 * @property integer $position
 * @property boolean $enabled
 *
 * @property boolean $notified
 *
 * @method static Lesson[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Lesson|null fetchOne($cond = null, $sort = null)
 * @method static Lesson      fetchObject($cond = null, $sort = null)
 */
class Lesson extends Model
{
    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->start <= time();
    }
}
