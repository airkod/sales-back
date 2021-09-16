<?php

declare(strict_types=1);

namespace App\Model;

use Light\Model;

/**
 * @collection Webinar
 *
 * @property string $id
 *
 * @property string $title
 * @property string $description
 * @property string $link
 * @property integer $start
 *
 * @property integer $position
 * @property boolean $enabled
 * @property boolean $notified
 *
 * @method static Webinar[]    fetchAll($cond = null, $sort = null, int $count = null, int $offset = null)
 * @method static Webinar|null fetchOne($cond = null, $sort = null)
 * @method static Webinar      fetchObject($cond = null, $sort = null)
 */
class Webinar extends Model
{
    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->start <= time() + 60 * 60 * 24;
    }
}
